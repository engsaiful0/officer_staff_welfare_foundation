<?php

namespace App\Http\Controllers;

use App\Models\HpsmCollection;
use App\Models\HpsmInstallment;
use App\Models\HpsmOpeningAccount;
use App\Services\Hpsm\HpsmCollectionAllocator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use RuntimeException;

class HpsmCollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = HpsmCollection::with(['openingAccount.member', 'installment']);

        if ($request->filled('hpsm_opening_account_id')) {
            $query->where('hpsm_opening_account_id', $request->integer('hpsm_opening_account_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('collection_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('collection_date', '<=', $request->date_to);
        }

        $collections = $query->orderByDesc('collection_date')->orderByDesc('id')->paginate(40)->withQueryString();
        $accounts = HpsmOpeningAccount::with('member')->orderBy('account_no')->get();

        return view('hpsm_collections.index', compact('collections', 'accounts'));
    }

    public function create(Request $request)
    {
        $accounts = HpsmOpeningAccount::with('member')
            ->where('status', 'active')
            ->orderBy('account_no')
            ->get();

        $selectedAccount = null;
        $payableInstallments = collect();

        if ($request->filled('hpsm_opening_account_id')) {
            $selectedAccount = HpsmOpeningAccount::with([
                'member',
                'installments' => fn ($q) => $q->orderBy('installment_no'),
            ])->find($request->integer('hpsm_opening_account_id'));

            if ($selectedAccount) {
                $payableInstallments = $selectedAccount->installments->filter(function (HpsmInstallment $i) {
                    return bccomp($i->totalDue(), '0', 2) === 1;
                })->values();
            }
        }

        return view('hpsm_collections.create', compact('accounts', 'selectedAccount', 'payableInstallments'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hpsm_opening_account_id' => ['required', 'exists:hpsm_opening_accounts,id'],
            'hpsm_installment_id' => ['required', 'exists:hpsm_installments,id'],
            'collection_date' => ['required', 'date'],
            'total_collected' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['nullable', 'string', 'max:255'],
            'transaction_no' => ['nullable', 'string', 'max:255'],
            'remarks' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $account = HpsmOpeningAccount::findOrFail((int) $request->hpsm_opening_account_id);

        $installment = HpsmInstallment::where('id', (int) $request->hpsm_installment_id)
            ->where('hpsm_opening_account_id', $account->id)
            ->first();

        if (! $installment) {
            return redirect()->back()->withErrors([
                'hpsm_installment_id' => 'Selected installment does not belong to this account.',
            ])->withInput();
        }

        try {
            $allocator = new HpsmCollectionAllocator;
            $total = number_format((float) $request->total_collected, 2, '.', '');
            $collection = $allocator->apply($account, $installment, $total, [
                'collection_date' => $request->collection_date,
                'payment_method' => $request->payment_method,
                'transaction_no' => $request->transaction_no,
                'remarks' => $request->remarks,
                'collected_by' => auth()->id(),
            ]);

            return redirect()
                ->route('hpsm-collections.receipt', $collection)
                ->with('success', 'Collection recorded successfully.');
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Collection failed: ' . $e->getMessage())->withInput();
        }
    }

    public function show(HpsmCollection $hpsm_collection)
    {
        $hpsm_collection->load(['openingAccount.member', 'installment']);

        return view('hpsm_collections.show', ['collection' => $hpsm_collection]);
    }

    public function receipt(HpsmCollection $hpsm_collection)
    {
        $hpsm_collection->load(['openingAccount.member', 'installment']);

        return view('hpsm_collections.receipt', ['collection' => $hpsm_collection]);
    }
}
