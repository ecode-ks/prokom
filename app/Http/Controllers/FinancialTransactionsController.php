<?php

namespace App\Http\Controllers;

use App\Services\FinancialTransactionsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinancialTransactionsController extends Controller
{
    protected FinancialTransactionsService $service;
    public function __construct(FinancialTransactionsService $service)
    {
        $this->service = $service;
    }
    //---------------
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:revenue,expense',
            'amount' => 'required|numeric|min:0.01|max:99999999999.99',
            'comment' => 'required|string|min:1',
        ]);
        if ($validator->fails()) {
            return redirect()->route('financial.transactions')->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $data = $request->only(['type', 'amount', 'comment']);
            $data['company_id'] = session('company_id');
            try {
                $this->service->createTransaction($data);
                return redirect()->route('financial.transactions')->with('success', __('pages.fin_created'));
            } catch (\Exception $e) {
                return redirect()->route('financial.transactions')->with('error', __('pages.try_again'))->withInput();
            }
        }
    }
    //---------------
    public function read(Request $request)
    {
        $page = 'Revenue & Expenses';
        $search = $request->query('search', '');
        $companyId = session('company_id');
        $transactions = $this->service->getTransactions(10, $search, $companyId);
        $revenues =  $this->service->getRevenues($companyId);
        $expenses =  $this->service->getExpenses($companyId);
        return view('management.financial-transactions')
            ->with('page', $page)
            ->with('revenues', $revenues)
            ->with('expenses', $expenses)
            ->with('transactions', $transactions);
    }
    //---------------
    public function report(Request $request)
    {
        $page = 'Financial Report';
        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date' => 'nullable|date|after_or_equal:from_date',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->errors()->first())->withInput();
        }

        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $companyId = session('company_id');
        if (empty($fromDate) && empty($toDate)) {
            $fromDate = Carbon::now()->startOfMonth()->toDateString();
            $toDate = Carbon::now()->endOfMonth()->toDateString();
        } elseif (empty($fromDate)) {
            $fromDate = $toDate;
        } elseif (empty($toDate)) {
            $toDate = $fromDate;
        }

        $report = $this->service->getFinancialReport($fromDate, $toDate, 10, $companyId);

        return view('management.financial-report')
            ->with('page', $page)
            ->with('report', $report)
            ->with('fromDate', $fromDate)
            ->with('toDate', $toDate);
    }
    //---------------
    public function edit(int $id)
    {
        $page = 'Revenue & Expenses';
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('financial.transactions')->with('error', __('pages.fin_not_found'));
        } else {
            $editTransaction = $this->service->getTransactionById($id, $companyId);
            return view('management.financial-transactions')
                ->with('page', $page)
                ->with('editTransaction', $editTransaction);
        }
    }
    //---------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|min:1|exists:financial_transactions,id',
            'type' => 'required|string|in:revenue,expense',
            'amount' => 'required|numeric|min:0.01|max:99999999999.99',
            'comment' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return back()->with('error', __('pages.fields_invalid'))->withInput();
        } else {
            $id = $request->id;
            $data = $request->only(['type', 'amount', 'comment']);
            $companyId = session('company_id');
            try {
                $this->service->updateTransaction($id, $data, $companyId);
                return redirect()->route('financial.transactions')->with('success', __('pages.fin_updated'));
            } catch (\Exception $e) {
                return redirect()->route('financial.transactions')->with('error', __('pages.try_again'));
            }
        }
    }
    //---------------
    public function delete(int $id)
    {
        $companyId = session('company_id');
        if (!$this->service->findOrFail($id, $companyId)) {
            return redirect()->route('financial.transactions')->with('error', __('pages.fin_not_found'));
        } else {
            try {
                $this->service->deleteTransaction($id, $companyId);
                return redirect()->route('financial.transactions')->with('success', __('pages.fin_deleted'));
            } catch (\Exception $e) {
                return redirect()->route('financial.transactions')->with('error', __('pages.try_again'));
            }
        }
    }
}
