<?php

namespace App\Controllers\Accounts;

use App\Controllers\BaseController;

class Billing extends BaseController
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

        // Get all bills with patient information and payments
        $allBills = $db->table('bills b')
            ->select('b.*, 
                COALESCE(b.invoice_number, CONCAT("INV-", YEAR(b.created_at), "-", LPAD(b.id, 4, "0"))) as invoice_number,
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                p.id as patient_id,
                COALESCE(SUM(pay.amount), 0) as paid_amount,
                (b.total_amount - COALESCE(SUM(pay.amount), 0)) as remaining_amount,
                a.id as admission_id', false)
            ->join('patients p', 'p.id = b.patient_id', 'left')
            ->join('payments pay', 'pay.bill_id = b.id', 'left')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->groupBy('b.id')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Add patient_type to each bill and group by patient
        $billsByPatient = [];
        foreach ($allBills as $bill) {
            $bill['patient_type'] = !empty($bill['admission_id']) ? 'inpatient' : 'outpatient';
            $patientId = $bill['patient_id'];
            
            if (!isset($billsByPatient[$patientId])) {
                $billsByPatient[$patientId] = [
                    'patient_id' => $patientId,
                    'patient_name' => $bill['patient_name'],
                    'patient_type' => $bill['patient_type'],
                    'admission_id' => $bill['admission_id'],
                    'total_amount' => 0,
                    'paid_amount' => 0,
                    'remaining_amount' => 0,
                    'bills' => [],
                    'has_pending' => false,
                    'has_paid' => false,
                ];
            }
            
            $billsByPatient[$patientId]['bills'][] = $bill;
            $billsByPatient[$patientId]['total_amount'] += floatval($bill['total_amount']);
            $billsByPatient[$patientId]['paid_amount'] += floatval($bill['paid_amount']);
            $billsByPatient[$patientId]['remaining_amount'] += floatval($bill['remaining_amount']);
            
            if (floatval($bill['remaining_amount']) > 0) {
                $billsByPatient[$patientId]['has_pending'] = true;
            }
            if (floatval($bill['paid_amount']) > 0) {
                $billsByPatient[$patientId]['has_paid'] = true;
            }
        }
        
        // Convert to array for view
        $bills = array_values($billsByPatient);

        // Calculate statistics
        $totalBills = count($bills);
        $paidBills = count(array_filter($bills, fn($b) => ($b['status'] ?? 'pending') === 'paid'));
        $pendingBills = count(array_filter($bills, fn($b) => ($b['status'] ?? 'pending') === 'pending'));
        $totalRevenue = array_sum(array_column($bills, 'total_amount'));
        $totalPaid = array_sum(array_column($bills, 'paid_amount'));

        // Get all payments for transaction history
        $payments = $db->table('payments pay')
            ->select('pay.*, b.id as bill_id, b.bill_type, 
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                COALESCE(pay.payment_date, DATE(pay.created_at)) as display_date')
            ->join('bills b', 'b.id = pay.bill_id', 'left')
            ->join('patients p', 'p.id = b.patient_id', 'left')
            ->orderBy('COALESCE(pay.payment_date, DATE(pay.created_at))', 'DESC', false)
            ->orderBy('pay.created_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();

        // Get all patients for bill creation dropdown
        $patients = $db->table('patients p')
            ->select('p.id, CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name')
            ->orderBy('p.last_name', 'ASC')
            ->orderBy('p.first_name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Billing & Invoices | Accounts Panel',
            'bills' => $bills,
            'payments' => $payments,
            'patients' => $patients,
            'totalBills' => $totalBills,
            'paidBills' => $paidBills,
            'pendingBills' => $pendingBills,
            'totalRevenue' => $totalRevenue,
            'totalPaid' => $totalPaid,
        ];

        return view('accounts/billing', $data);
    }

    public function store()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('accountant')) {
            return redirect()->to(site_url('dashboard'));
        }

        $request = $this->request;
        $db = db_connect();

        $patientId = (int) $request->getPost('patient_id');
        $billType = trim($request->getPost('bill_type') ?? '');
        $totalAmount = floatval($request->getPost('total_amount') ?? 0);
        $description = trim($request->getPost('description') ?? '');

        if (!$patientId || !$billType || $totalAmount <= 0) {
            session()->setFlashdata('error', 'Patient, bill type, and amount are required.');
            return redirect()->to(site_url('accounts/billing'));
        }

        // Generate invoice number: INV-YYYY-NNNN
        $year = date('Y');
        $lastInvoice = $db->table('bills')
            ->like('invoice_number', "INV-{$year}-", 'after')
            ->orderBy('invoice_number', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();
        
        if ($lastInvoice && !empty($lastInvoice['invoice_number'])) {
            // Extract the sequence number
            $parts = explode('-', $lastInvoice['invoice_number']);
            $sequence = isset($parts[2]) ? (int) $parts[2] : 0;
            $sequence++;
        } else {
            $sequence = 1;
        }
        
        $invoiceNumber = sprintf('INV-%s-%04d', $year, $sequence);

        $data = [
            'patient_id' => $patientId,
            'invoice_number' => $invoiceNumber,
            'bill_type' => $billType,
            'total_amount' => $totalAmount,
            'description' => $description ?: null,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('bills')->insert($data);

        session()->setFlashdata('success', 'Bill created successfully.');
        return redirect()->to(site_url('accounts/billing'));
    }

    public function getPatientBills($patientId)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        if (!$this->hasRole('accountant')) {
            return $this->response->setJSON(['error' => 'Forbidden'])->setStatusCode(403);
        }

        $db = db_connect();

        // Get all pending bills for this patient
        $bills = $db->table('bills b')
            ->select('b.*, 
                COALESCE(b.invoice_number, CONCAT("INV-", YEAR(b.created_at), "-", LPAD(b.id, 4, "0"))) as invoice_number,
                COALESCE(SUM(pay.amount), 0) as paid_amount,
                (b.total_amount - COALESCE(SUM(pay.amount), 0)) as remaining_amount', false)
            ->join('payments pay', 'pay.bill_id = b.id', 'left')
            ->where('b.patient_id', $patientId)
            ->groupBy('b.id')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['bills' => $bills]);
    }
}

