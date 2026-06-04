<?php

namespace App\Http\Controllers;

use App\Models\HpsmCollection;
use App\Models\HpsmInstallment;
use App\Models\HpsmOpeningAccount;
use App\Models\Member;
use Illuminate\Http\Request;

class HpsmReportController extends Controller
{
    public function dueReport(Request $request)
    {
        $query = HpsmInstallment::query()
            ->with(['openingAccount.member'])
            ->whereHas('openingAccount', fn ($q) => $q->where('status', 'active'))
            ->whereIn('payment_status', ['pending', 'partial'])
            ->where('due_amount', '>', 0)
            ->orderBy('installment_date')
            ->orderBy('installment_no');

        if ($request->filled('member_id')) {
            $query->whereHas('openingAccount', fn ($q) => $q->where('member_id', $request->integer('member_id')));
        }

        if ($request->filled('account_no')) {
            $query->whereHas('openingAccount', fn ($q) => $q->where('account_no', 'like', '%' . $request->account_no . '%'));
        }

        $rows = $query->paginate(60)->withQueryString();
        $members = Member::orderBy('name')->select('id', 'name', 'member_unique_id')->get();

        return view('hpsm_reports.due_report', compact('rows', 'members'));
    }

    public function collectionReport(Request $request)
    {
        $q = HpsmCollection::with(['openingAccount.member', 'installment']);

        if ($request->filled('hpsm_opening_account_id')) {
            $q->where('hpsm_opening_account_id', $request->integer('hpsm_opening_account_id'));
        }

        if ($request->filled('date_from')) {
            $q->whereDate('collection_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $q->whereDate('collection_date', '<=', $request->date_to);
        }

        $collections = $q->orderByDesc('collection_date')->orderByDesc('id')->paginate(60)->withQueryString();
        $accounts = HpsmOpeningAccount::with('member')->orderBy('account_no')->get();

        return view('hpsm_reports.collection_report', compact('collections', 'accounts'));
    }

    public function memberStatement(Request $request)
    {
        $members = Member::orderBy('name')->select('id', 'name', 'member_unique_id')->get();
        $member = null;
        $accounts = collect();
        $timeline = collect();

        if ($request->filled('member_id')) {
            $member = Member::find($request->integer('member_id'));
            if ($member) {
                $accounts = HpsmOpeningAccount::where('member_id', $member->id)
                    ->with(['installments' => fn ($q) => $q->orderBy('installment_no')])
                    ->orderByDesc('opening_date')
                    ->get();

                $ids = $accounts->pluck('id')->all();
                if ($ids !== []) {
                    $timeline = HpsmCollection::whereIn('hpsm_opening_account_id', $ids)
                        ->with(['openingAccount', 'installment'])
                        ->orderByDesc('collection_date')
                        ->orderByDesc('id')
                        ->get();
                }
            }
        }

        return view('hpsm_reports.member_statement', compact('members', 'member', 'accounts', 'timeline'));
    }
}
