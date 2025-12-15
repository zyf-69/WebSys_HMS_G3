<?php

namespace App\Controllers\Accounts;

use App\Controllers\BaseController;

class Payments extends BaseController
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
        $filterPatient = $request->getGet('patient_id') ? (int) $request->getGet('patient_id') : null;
        $filterMethod = trim($request->getGet('payment_method') ?? '');
        $filterType = trim($request->getGet('type') ?? ''); // payment or refund
        $filterDateFrom = trim($request->getGet('date_from') ?? '');
        $filterDateTo = trim($request->getGet('date_to') ?? '');

        // Build payments query with filters
        $builder = $db->table('payments pay')
            ->select('pay.*, b.id as bill_id, b.bill_type, b.total_amount as bill_total, b.status as bill_status,
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                p.id as patient_id,
                COALESCE(pay.payment_date, DATE(pay.created_at)) as display_date')
            ->join('bills b', 'b.id = pay.bill_id', 'left')
            ->join('patients p', 'p.id = b.patient_id', 'left');

        // Apply filters
        if (!empty($filterPatient)) {
            $builder->where('p.id', $filterPatient);
        }

        if (!empty($filterMethod)) {
            $builder->where('pay.payment_method', $filterMethod);
        }

        if ($filterType === 'refund') {
            $builder->where('pay.amount <', 0);
        } elseif ($filterType === 'payment') {
            $builder->where('pay.amount >=', 0);
        }

        if (!empty($filterDateFrom)) {
            $builder->where('DATE(COALESCE(pay.payment_date, pay.created_at)) >=', $filterDateFrom);
        }

        if (!empty($filterDateTo)) {
            $builder->where('DATE(COALESCE(pay.payment_date, pay.created_at)) <=', $filterDateTo);
        }

        try {
            $payments = $builder
                ->orderBy('COALESCE(pay.payment_date, DATE(pay.created_at))', 'DESC', false)
                ->orderBy('pay.created_at', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching payments: ' . $e->getMessage());
            log_message('error', 'Query: ' . $db->getLastQuery());
            session()->setFlashdata('error', 'Error loading payments. Please try again.');
            $payments = [];
        }

        // Calculate statistics (from all payments, not filtered)
        $allPayments = $db->table('payments pay')
            ->select('pay.*')
            ->get()
            ->getResultArray();

        $totalPayments = count($allPayments);
        $totalAmount = array_sum(array_column($allPayments, 'amount'));
        
        // Group by payment method
        $byMethod = [];
        foreach ($allPayments as $payment) {
            $method = $payment['payment_method'] ?? 'cash';
            if (!isset($byMethod[$method])) {
                $byMethod[$method] = 0;
            }
            $byMethod[$method] += floatval($payment['amount']);
        }

        // Get all patients for patient filter dropdown
        try {
            $patients = $db->table('patients p')
                ->select('p.id, CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name')
                ->orderBy('p.last_name', 'ASC')
                ->orderBy('p.first_name', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error fetching patients: ' . $e->getMessage());
            $patients = [];
        }

        // Get all bills for payment/refund recording
        $allBills = $db->table('bills b')
            ->select('b.*, 
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                COALESCE(SUM(pay.amount), 0) as paid_amount,
                (b.total_amount - COALESCE(SUM(pay.amount), 0)) as remaining_amount')
            ->join('patients p', 'p.id = b.patient_id', 'left')
            ->join('payments pay', 'pay.bill_id = b.id', 'left')
            ->groupBy('b.id')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Group payments by patient for summary
        $paymentsByPatient = [];
        foreach ($payments as $payment) {
            $patientId = $payment['patient_id'] ?? 0;
            if (!isset($paymentsByPatient[$patientId])) {
                $paymentsByPatient[$patientId] = [
                    'patient_name' => $payment['patient_name'] ?? 'Unknown',
                    'total_paid' => 0,
                    'total_refunded' => 0,
                    'count' => 0,
                ];
            }
            $amount = floatval($payment['amount'] ?? 0);
            if ($amount >= 0) {
                $paymentsByPatient[$patientId]['total_paid'] += $amount;
            } else {
                $paymentsByPatient[$patientId]['total_refunded'] += abs($amount);
            }
            $paymentsByPatient[$patientId]['count']++;
        }

        $data = [
            'title' => 'Payments | Accounts Panel',
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'totalAmount' => $totalAmount,
            'byMethod' => $byMethod,
            'bills' => $allBills,
            'patients' => $patients,
            'paymentsByPatient' => $paymentsByPatient,
            'filters' => [
                'patient_id' => $filterPatient,
                'payment_method' => $filterMethod,
                'type' => $filterType,
                'date_from' => $filterDateFrom,
                'date_to' => $filterDateTo,
            ],
        ];

        return view('accounts/payments', $data);
    }

    public function recordRefund()
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

        $billId = $request->getPost('bill_id');
        $refundAmount = floatval($request->getPost('refund_amount'));
        $reason = trim($request->getPost('reason') ?? '');

        if (!$billId || $refundAmount <= 0) {
            session()->setFlashdata('error', 'Invalid refund data.');
            return redirect()->to(site_url('accounts/payments'));
        }

        $db->transStart();

        // Record refund as negative payment
        $db->table('payments')->insert([
            'bill_id' => $billId,
            'amount' => -$refundAmount, // Negative amount for refunds
            'payment_date' => date('Y-m-d'),
            'payment_method' => 'refund',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Update bill status if needed
        $bill = $db->table('bills')->where('id', $billId)->get()->getRowArray();
        if ($bill) {
            $totalPaid = $db->table('payments')
                ->selectSum('amount')
                ->where('bill_id', $billId)
                ->get()
                ->getRowArray();
            
            $paidTotal = floatval($totalPaid['amount'] ?? 0);
            if ($paidTotal <= 0 || $paidTotal < floatval($bill['total_amount'])) {
                $db->table('bills')
                    ->where('id', $billId)
                    ->update(['status' => 'pending', 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }

        $db->transComplete();

        session()->setFlashdata('success', 'Refund recorded successfully.');
        return redirect()->to(site_url('accounts/payments'));
    }

    public function recordPayment()
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

        $patientId = $request->getPost('patient_id');
        $paymentAmount = floatval($request->getPost('payment_amount'));
        $paymentDate = $request->getPost('payment_date') ?: date('Y-m-d');
        $paymentMethod = trim($request->getPost('payment_method') ?? 'cash');
        $notes = trim($request->getPost('notes') ?? '');

        if (!$patientId || $paymentAmount <= 0) {
            session()->setFlashdata('error', 'Please provide a patient and valid payment amount.');
            return redirect()->to(site_url('accounts/billing'));
        }

        // Get all pending bills for this patient
        $bills = $db->table('bills b')
            ->select('b.*, COALESCE(SUM(pay.amount), 0) as current_paid')
            ->join('payments pay', 'pay.bill_id = b.id', 'left')
            ->where('b.patient_id', $patientId)
            ->groupBy('b.id')
            ->get()
            ->getResultArray();

        if (empty($bills)) {
            session()->setFlashdata('error', 'No bills found for this patient.');
            return redirect()->to(site_url('accounts/billing'));
        }

        // Calculate total remaining
        $totalRemaining = 0;
        $pendingBills = [];
        foreach ($bills as $bill) {
            $currentPaid = floatval($bill['current_paid'] ?? 0);
            $billTotal = floatval($bill['total_amount']);
            $remaining = $billTotal - $currentPaid;
            
            if ($remaining > 0) {
                $pendingBills[] = [
                    'id' => $bill['id'],
                    'total' => $billTotal,
                    'current_paid' => $currentPaid,
                    'remaining' => $remaining,
                ];
                $totalRemaining += $remaining;
            }
        }

        if (empty($pendingBills)) {
            session()->setFlashdata('error', 'No pending bills found for this patient.');
            return redirect()->to(site_url('accounts/billing'));
        }

        // Validate payment amount doesn't exceed total remaining
        if ($paymentAmount > $totalRemaining) {
            session()->setFlashdata('error', 'Payment amount cannot exceed total remaining balance of ₱' . number_format($totalRemaining, 2));
            return redirect()->to(site_url('accounts/billing'));
        }

        $db->transStart();

        // Distribute payment proportionally across pending bills
        $paymentDistributed = 0;
        $lastBillIndex = count($pendingBills) - 1;

        foreach ($pendingBills as $index => $bill) {
            // Calculate payment amount for this bill
            if ($index === $lastBillIndex) {
                // For the last bill, use remaining payment to avoid rounding issues
                $billPaymentAmount = $paymentAmount - $paymentDistributed;
            } else {
                // Proportional distribution based on remaining amount
                $proportion = $bill['remaining'] / $totalRemaining;
                $billPaymentAmount = round($paymentAmount * $proportion, 2);
            }
            
            // Ensure we don't exceed the bill's remaining amount
            $billPaymentAmount = min($billPaymentAmount, $bill['remaining']);
            
            // Track distributed amount (only for non-last bills)
            if ($index !== $lastBillIndex) {
                $paymentDistributed += $billPaymentAmount;
            }

            if ($billPaymentAmount > 0) {
                // Record payment for this bill
                $db->table('payments')->insert([
                    'bill_id' => $bill['id'],
                    'amount' => $billPaymentAmount,
                    'payment_date' => $paymentDate,
                    'payment_method' => $paymentMethod,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // Calculate new total paid for this bill
                $totalPaid = $db->table('payments')
                    ->selectSum('amount')
                    ->where('bill_id', $bill['id'])
                    ->get()
                    ->getRowArray();
                
                $paidTotal = floatval($totalPaid['amount'] ?? 0);

                // Update bill status
                $newStatus = ($paidTotal >= $bill['total']) ? 'paid' : 'pending';

                $db->table('bills')
                    ->where('id', $bill['id'])
                    ->update([
                        'status' => $newStatus,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Failed to record payment. Please try again.');
        } else {
            $billCount = count($pendingBills);
            session()->setFlashdata('success', "Payment of ₱" . number_format($paymentAmount, 2) . " recorded successfully for {$billCount} bill(s).");
        }

        return redirect()->to(site_url('accounts/billing'));
    }
}

