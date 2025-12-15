<?php

namespace App\Controllers\Accounts;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('accountant')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();
        $request = $this->request;

        // Get filter parameters
        $reportType = $request->getGet('type') ?? 'daily';
        $startDate = $request->getGet('start_date');
        $endDate = $request->getGet('end_date');
        $year = $request->getGet('year') ?? date('Y');
        $month = $request->getGet('month') ?? date('n');

        $data = [
            'title' => 'Financial Reports | Accounts Panel',
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'year' => $year,
            'month' => $month,
        ];

        return view('accounts/reports', $data);
    }

    public function generate()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('accountant')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();
        $request = $this->request;

        $reportType = $request->getPost('report_type') ?? 'daily';
        $format = $request->getPost('format') ?? 'html';
        $startDate = $request->getPost('start_date');
        $endDate = $request->getPost('end_date');
        $year = intval($request->getPost('year') ?? date('Y'));
        $month = intval($request->getPost('month') ?? date('n'));

        // Calculate date range based on report type
        if ($reportType === 'daily') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($reportType === 'monthly') {
            $startDate = date("{$year}-{$month}-01");
            $endDate = date("Y-m-t", strtotime($startDate));
        } elseif ($reportType === 'yearly') {
            $startDate = "{$year}-01-01";
            $endDate = "{$year}-12-31";
        } elseif ($reportType === 'custom' && $startDate && $endDate) {
            // Use provided dates
        } else {
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
        }

        // Get revenue (payments) - use COALESCE to handle NULL payment_date
        $revenueQuery = $db->table('payments')
            ->selectSum('amount')
            ->where('DATE(COALESCE(payment_date, created_at)) >=', $startDate)
            ->where('DATE(COALESCE(payment_date, created_at)) <=', $endDate)
            ->where('amount >', 0) // Exclude refunds
            ->get();
        $totalRevenue = floatval($revenueQuery->getRowArray()['amount'] ?? 0);

        // Get expenses
        $expensesQuery = $db->table('expenses')
            ->selectSum('amount')
            ->where('expense_date >=', $startDate)
            ->where('expense_date <=', $endDate)
            ->get();
        $totalExpenses = floatval($expensesQuery->getRowArray()['amount'] ?? 0);

        // Get detailed transactions
        $payments = $db->table('payments pay')
            ->select('pay.*, b.bill_type,
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                COALESCE(pay.payment_date, DATE(pay.created_at)) as display_date')
            ->join('bills b', 'b.id = pay.bill_id', 'left')
            ->join('patients p', 'p.id = b.patient_id', 'left')
            ->where('DATE(COALESCE(pay.payment_date, pay.created_at)) >=', $startDate)
            ->where('DATE(COALESCE(pay.payment_date, pay.created_at)) <=', $endDate)
            ->orderBy('COALESCE(pay.payment_date, DATE(pay.created_at))', 'ASC', false)
            ->get()
            ->getResultArray();

        $expenses = $db->table('expenses')
            ->select('*')
            ->where('expense_date >=', $startDate)
            ->where('expense_date <=', $endDate)
            ->orderBy('expense_date', 'ASC')
            ->get()
            ->getResultArray();

        // Calculate VAT (assuming 12% VAT on revenue)
        $vatRate = 0.12;
        $vatAmount = $totalRevenue * $vatRate;
        $serviceChargeRate = 0.10; // 10% service charge
        $serviceCharge = $totalRevenue * $serviceChargeRate;

        $netProfit = $totalRevenue - $totalExpenses - $vatAmount - $serviceCharge;

        $reportData = [
            'reportType' => $reportType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalRevenue' => $totalRevenue,
            'totalExpenses' => $totalExpenses,
            'vatAmount' => $vatAmount,
            'serviceCharge' => $serviceCharge,
            'netProfit' => $netProfit,
            'payments' => $payments,
            'expenses' => $expenses,
        ];

        if ($format === 'pdf' || $format === 'excel') {
            // For PDF/Excel export, redirect to export method or handle separately
            session()->setFlashdata('error', 'PDF and Excel export functionality will be implemented.');
            return redirect()->to(site_url('accounts/reports'));
        }

        $data = [
            'title' => 'Financial Report | Accounts Panel',
            'reportData' => $reportData,
        ];

        return view('accounts/reports_generate', $data);
    }
}

