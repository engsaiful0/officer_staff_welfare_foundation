<?php

namespace App\Http\Controllers;

use App\Models\HpsmOpeningAccount;
use App\Models\Member;
use App\Services\Hpsm\HpsmInstallmentScheduleGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HpsmOpeningAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = HpsmOpeningAccount::with(['member:id,name,unique_id']);

        if ($request->filled('member_id')) {
            $query->where('member_id', $request->integer('member_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $accounts = $query->orderByDesc('created_at')->paginate(30)->withQueryString();
        $members = Member::orderBy('name')->select('id', 'name', 'unique_id')->get();

        return view('hpsm_opening_accounts.index', compact('accounts', 'members'));
    }

    public function create(Request $request)
    {
        $members = Member::orderBy('name')->select('id', 'name', 'unique_id')->get();
        $member = null;
        if ($request->filled('member_id')) {
            $member = Member::select('id', 'name', 'unique_id')->find($request->integer('member_id'));
        }

        return view('hpsm_opening_accounts.create', compact('members', 'member'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->accountRules());

        $validator->after(function ($validator) use ($request) {
            $exists = HpsmOpeningAccount::where('member_id', $request->member_id)
                ->where('status', 'active')
                ->exists();
            if ($exists) {
                $validator->errors()->add('member_id', 'This member already has an active HPSM opening account.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $monthlyPrincipal = round((float) $request->balance_principal / (float) $request->remaining_duration_months, 2);
        $totalOpening = round(
            (float) $request->balance_principal + (float) $request->balance_pre_rent + (float) $request->current_rent,
            2
        );

        try {
            $account = DB::transaction(function () use ($request, $monthlyPrincipal, $totalOpening) {
                $account = HpsmOpeningAccount::create([
                    'member_id' => $request->member_id,
                    'account_no' => $this->nextAccountNo(),
                    'balance_principal' => $request->balance_principal,
                    'balance_pre_rent' => $request->balance_pre_rent,
                    'current_rent' => $request->current_rent,
                    'annual_profit_rate' => $request->annual_profit_rate,
                    'remaining_duration_months' => $request->remaining_duration_months,
                    'monthly_principal' => $monthlyPrincipal,
                    'current_outstanding_principal' => $request->balance_principal,
                    'total_opening_balance' => $totalOpening,
                    'opening_date' => $request->opening_date,
                    'status' => 'active',
                    'remarks' => $request->remarks,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                (new HpsmInstallmentScheduleGenerator)->replaceSchedule($account);

                return $account;
            });

            return redirect()
                ->route('hpsm-opening-accounts.show', $account)
                ->with('success', 'HPSM opening account created and installment schedule generated.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to save account: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(HpsmOpeningAccount $hpsm_opening_account)
    {
        $hpsm_opening_account->load([
            'member',
            'installments' => fn ($q) => $q->orderBy('installment_no'),
            'collections' => fn ($q) => $q->latest()->limit(20),
        ]);

        return view('hpsm_opening_accounts.show', ['account' => $hpsm_opening_account]);
    }

    public function edit(HpsmOpeningAccount $hpsm_opening_account)
    {
        $members = Member::orderBy('name')->select('id', 'name', 'unique_id')->get();
        $mayReschedule = $hpsm_opening_account->collections()->doesntExist();

        return view('hpsm_opening_accounts.edit', [
            'account' => $hpsm_opening_account,
            'members' => $members,
            'mayReschedule' => $mayReschedule,
        ]);
    }

    public function update(Request $request, HpsmOpeningAccount $hpsm_opening_account)
    {
        $validator = Validator::make($request->all(), $this->accountRules());

        $validator->after(function ($validator) use ($request, $hpsm_opening_account) {
            $exists = HpsmOpeningAccount::where('member_id', $request->member_id)
                ->where('status', 'active')
                ->where('id', '!=', $hpsm_opening_account->id)
                ->exists();
            if ($exists) {
                $validator->errors()->add('member_id', 'This member already has an active HPSM opening account.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $monthlyPrincipal = round((float) $request->balance_principal / (float) $request->remaining_duration_months, 2);
        $totalOpening = round(
            (float) $request->balance_principal + (float) $request->balance_pre_rent + (float) $request->current_rent,
            2
        );

        $requiresRescheduleFields = (
            $hpsm_opening_account->remaining_duration_months != $request->integer('remaining_duration_months')
            || (string) $hpsm_opening_account->balance_principal !== (string) $request->balance_principal
            || (string) $hpsm_opening_account->balance_pre_rent !== (string) $request->balance_pre_rent
            || (string) $hpsm_opening_account->current_rent !== (string) $request->current_rent
            || (string) $hpsm_opening_account->annual_profit_rate !== (string) $request->annual_profit_rate
            || $hpsm_opening_account->opening_date->toDateString() !== $request->opening_date
        );

        try {
            DB::transaction(function () use (
                $request,
                $hpsm_opening_account,
                $monthlyPrincipal,
                $totalOpening,
                $requiresRescheduleFields,
            ) {
                if ($requiresRescheduleFields && $hpsm_opening_account->collections()->exists()) {
                    throw new \RuntimeException(
                        'This account already has collections. Financial fields affecting the installment schedule cannot be changed.'
                    );
                }

                $hpsm_opening_account->fill([
                    'member_id' => $request->member_id,
                    'balance_principal' => $request->balance_principal,
                    'balance_pre_rent' => $request->balance_pre_rent,
                    'current_rent' => $request->current_rent,
                    'annual_profit_rate' => $request->annual_profit_rate,
                    'remaining_duration_months' => $request->remaining_duration_months,
                    'monthly_principal' => $monthlyPrincipal,
                    'total_opening_balance' => $totalOpening,
                    'opening_date' => $request->opening_date,
                    'remarks' => $request->remarks,
                    'updated_by' => auth()->id(),
                ]);

                if ($requiresRescheduleFields) {
                    $hpsm_opening_account->current_outstanding_principal = $request->balance_principal;
                }

                $hpsm_opening_account->save();

                if ($requiresRescheduleFields) {
                    (new HpsmInstallmentScheduleGenerator)->replaceSchedule($hpsm_opening_account->fresh());
                }
            });

            return redirect()
                ->route('hpsm-opening-accounts.show', $hpsm_opening_account)
                ->with('success', 'HPSM opening account updated.');
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(HpsmOpeningAccount $hpsm_opening_account)
    {
        if ($hpsm_opening_account->collections()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete an account that has collection records.');
        }

        DB::transaction(function () use ($hpsm_opening_account) {
            $hpsm_opening_account->installments()->delete();
            $hpsm_opening_account->updated_by = auth()->id();
            $hpsm_opening_account->save();
            $hpsm_opening_account->delete();
        });

        return redirect()->route('hpsm-opening-accounts.index')->with('success', 'Account deleted.');
    }

    public function schedule(HpsmOpeningAccount $hpsm_opening_account)
    {
        $hpsm_opening_account->load(['member', 'installments' => fn ($q) => $q->orderBy('installment_no')]);

        return view('hpsm_opening_accounts.schedule', ['account' => $hpsm_opening_account]);
    }

    public function ledger(HpsmOpeningAccount $hpsm_opening_account)
    {
        $hpsm_opening_account->load(['member']);

        $collections = $hpsm_opening_account->collections()->with('installment')->paginate(30);

        return view('hpsm_opening_accounts.ledger', [
            'account' => $hpsm_opening_account,
            'collections' => $collections,
        ]);
    }

    public function printSchedule(HpsmOpeningAccount $hpsm_opening_account)
    {
        $hpsm_opening_account->load(['member', 'installments' => fn ($q) => $q->orderBy('installment_no')]);

        return view('hpsm_opening_accounts.print_schedule', ['account' => $hpsm_opening_account]);
    }

    private function accountRules(): array
    {
        return [
            'member_id' => ['required', 'exists:members,id'],
            'balance_principal' => ['required', 'numeric', 'min:0'],
            'balance_pre_rent' => ['required', 'numeric', 'min:0'],
            'current_rent' => ['required', 'numeric', 'min:0'],
            'annual_profit_rate' => ['required', 'numeric', 'min:0'],
            'remaining_duration_months' => ['required', 'integer', 'min:1'],
            'opening_date' => ['required', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }

    /**
     * Auto-increment annual HPSM account numbers.
     */
    private function nextAccountNo(): string
    {
        $prefix = 'HPSM-' . date('Y') . '-';

        $last = HpsmOpeningAccount::withTrashed()
            ->where('account_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('account_no');

        $seq = 1;
        if ($last && preg_match('/(\d+)\s*$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
