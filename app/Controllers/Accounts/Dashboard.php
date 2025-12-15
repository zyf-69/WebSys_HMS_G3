<?php

namespace App\Controllers\Accounts;

use App\Controllers\BaseController;

class Dashboard extends BaseController
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

        // Today's date range
        $today = date('Y-m-d');
        
        // Revenue calculations (from payments) - use COALESCE to handle NULL payment_date
        $todayRevenue = $db->table('payments')
            ->selectSum('amount')
            ->where('DATE(COALESCE(payment_date, created_at))', $today)
            ->where('amount >', 0) // Exclude refunds
            ->get()
            ->getRowArray();
        $todayRevenueAmount = floatval($todayRevenue['amount'] ?? 0);

        $monthRevenue = $db->table('payments')
            ->selectSum('amount')
            ->where('MONTH(COALESCE(payment_date, created_at))', date('n'))
            ->where('YEAR(COALESCE(payment_date, created_at))', date('Y'))
            ->where('amount >', 0)
            ->get()
            ->getRowArray();
        $monthRevenueAmount = floatval($monthRevenue['amount'] ?? 0);

        $yearRevenue = $db->table('payments')
            ->selectSum('amount')
            ->where('YEAR(COALESCE(payment_date, created_at))', date('Y'))
            ->where('amount >', 0)
            ->get()
            ->getRowArray();
        $yearRevenueAmount = floatval($yearRevenue['amount'] ?? 0);

        // Expense calculations
        $todayExpenses = $db->table('expenses')
            ->selectSum('amount')
            ->where('expense_date', $today)
            ->get()
            ->getRowArray();
        $todayExpensesAmount = floatval($todayExpenses['amount'] ?? 0);

        $monthExpenses = $db->table('expenses')
            ->selectSum('amount')
            ->where('MONTH(expense_date)', date('n'))
            ->where('YEAR(expense_date)', date('Y'))
            ->get()
            ->getRowArray();
        $monthExpensesAmount = floatval($monthExpenses['amount'] ?? 0);

        $yearExpenses = $db->table('expenses')
            ->selectSum('amount')
            ->where('YEAR(expense_date)', date('Y'))
            ->get()
            ->getRowArray();
        $yearExpensesAmount = floatval($yearExpenses['amount'] ?? 0);

        // Profit calculations
        $todayProfit = $todayRevenueAmount - $todayExpensesAmount;
        $monthProfit = $monthRevenueAmount - $monthExpensesAmount;
        $yearProfit = $yearRevenueAmount - $yearExpensesAmount;

        // Count unpaid bills
        $unpaidBills = $db->table('bills')
            ->where('status', 'pending')
            ->countAllResults(false);

        // Count invoices today
        $invoicesToday = $db->table('bills')
            ->where('DATE(created_at)', $today)
            ->countAllResults(false);

        // Recent transactions (last 10)
        $recentTransactions = $db->table('payments pay')
            ->select('pay.*, b.bill_type,
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                COALESCE(pay.payment_date, DATE(pay.created_at)) as display_date')
            ->join('bills b', 'b.id = pay.bill_id', 'left')
            ->join('patients p', 'p.id = b.patient_id', 'left')
            ->orderBy('pay.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Accounts Dashboard | HMS System',
            'todayRevenue' => $todayRevenueAmount,
            'monthRevenue' => $monthRevenueAmount,
            'yearRevenue' => $yearRevenueAmount,
            'todayExpenses' => $todayExpensesAmount,
            'monthExpenses' => $monthExpensesAmount,
            'yearExpenses' => $yearExpensesAmount,
            'todayProfit' => $todayProfit,
            'monthProfit' => $monthProfit,
            'yearProfit' => $yearProfit,
            'unpaidBills' => $unpaidBills,
            'invoicesToday' => $invoicesToday,
            'recentTransactions' => $recentTransactions,
        ];

        return view('accounts/dashboard', $data);
    }
}
