<?php

namespace App\Controllers\Accounts;

use App\Controllers\BaseController;

class Expenses extends BaseController
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
        $filterMonth = $request->getGet('month');
        $filterYear = $request->getGet('year') ?? date('Y');
        $filterType = trim($request->getGet('type') ?? ''); // Pharmacy, Laboratory, Consultation, Procedure, Room & Board

        // Get all patients for filter
        $patients = $db->table('patients p')
            ->select('p.id, CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name')
            ->orderBy('p.last_name', 'ASC')
            ->orderBy('p.first_name', 'ASC')
            ->get()
            ->getResultArray();

        // Get bills (which are auto-generated from prescriptions, lab tests, etc.)
        $billsBuilder = $db->table('bills b')
            ->select('b.id, b.patient_id, b.bill_type, b.total_amount, b.description, b.created_at as expense_date,
                COALESCE(b.invoice_number, CONCAT("INV-", YEAR(b.created_at), "-", LPAD(b.id, 4, "0"))) as invoice_number,
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name')
            ->join('patients p', 'p.id = b.patient_id', 'left');

        if ($filterPatient) {
            $billsBuilder->where('b.patient_id', $filterPatient);
        }
        if ($filterMonth) {
            $billsBuilder->where('MONTH(b.created_at)', $filterMonth);
        }
        if ($filterYear) {
            $billsBuilder->where('YEAR(b.created_at)', $filterYear);
        }
        if ($filterType) {
            $billsBuilder->where('b.bill_type', $filterType);
        }

        $bills = $billsBuilder->orderBy('b.created_at', 'DESC')->get()->getResultArray();

        // Format expenses data
        $patientExpenses = [];
        foreach ($bills as $bill) {
            $patientExpenses[] = [
                'patient_id' => $bill['patient_id'],
                'patient_name' => $bill['patient_name'],
                'expense_type' => $bill['bill_type'] ?? 'N/A',
                'item_description' => $bill['description'] ?? ($bill['bill_type'] ?? 'N/A'),
                'amount' => floatval($bill['total_amount'] ?? 0),
                'expense_date' => $bill['expense_date'],
                'reference' => $bill['invoice_number'] ?? 'N/A',
                'source' => strtolower($bill['bill_type'] ?? 'other'),
            ];
        }

        // Calculate statistics
        $totalExpenses = array_sum(array_column($patientExpenses, 'amount'));
        $thisMonthExpenses = array_filter($patientExpenses, function($exp) {
            $expDate = $exp['expense_date'] ?? '';
            return date('Y-m', strtotime($expDate)) === date('Y-m');
        });
        $thisMonthTotal = array_sum(array_column($thisMonthExpenses, 'amount'));

        // Group by bill type
        $byType = [];
        foreach ($patientExpenses as $expense) {
            $type = $expense['expense_type'] ?? 'Other';
            if (!isset($byType[$type])) {
                $byType[$type] = 0;
            }
            $byType[$type] += floatval($expense['amount'] ?? 0);
        }

        // Group by patient if no patient filter
        $byPatient = [];
        if (!$filterPatient) {
            foreach ($patientExpenses as $expense) {
                $patientId = $expense['patient_id'] ?? 0;
                if (!isset($byPatient[$patientId])) {
                    $byPatient[$patientId] = [
                        'patient_name' => $expense['patient_name'] ?? 'Unknown',
                        'total' => 0,
                        'count' => 0,
                    ];
                }
                $byPatient[$patientId]['total'] += floatval($expense['amount'] ?? 0);
                $byPatient[$patientId]['count']++;
            }
        }

        // Get unique bill types for filter
        $billTypes = $db->table('bills')
            ->select('bill_type')
            ->distinct()
            ->where('bill_type IS NOT NULL')
            ->where('bill_type !=', '')
            ->orderBy('bill_type', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Patient Expenses | Accounts Panel',
            'patientExpenses' => $patientExpenses,
            'patients' => $patients,
            'billTypes' => array_column($billTypes, 'bill_type'),
            'totalExpenses' => $totalExpenses,
            'thisMonthTotal' => $thisMonthTotal,
            'byType' => $byType,
            'byPatient' => $byPatient,
            'filterPatient' => $filterPatient,
            'filterMonth' => $filterMonth,
            'filterYear' => $filterYear,
            'filterType' => $filterType,
        ];

        return view('accounts/expenses', $data);
    }

    public function getPatientDetails($patientId)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        if (!$this->hasRole('accountant')) {
            return $this->response->setJSON(['error' => 'Forbidden'])->setStatusCode(403);
        }

        $db = db_connect();

        // Get patient information
        $patient = $db->table('patients p')
            ->select('p.id, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender,
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                a.id as admission_id')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->where('p.id', $patientId)
            ->get()
            ->getRowArray();

        if (!$patient) {
            return $this->response->setJSON(['error' => 'Patient not found'])->setStatusCode(404);
        }

        $patient['patient_type'] = !empty($patient['admission_id']) ? 'Inpatient' : 'Outpatient';

        // Get all bills for this patient with payments
        $bills = $db->table('bills b')
            ->select('b.*, 
                COALESCE(b.invoice_number, CONCAT("INV-", YEAR(b.created_at), "-", LPAD(b.id, 4, "0"))) as invoice_number,
                COALESCE(SUM(pay.amount), 0) as paid_amount,
                (b.total_amount - COALESCE(SUM(pay.amount), 0)) as remaining_amount')
            ->join('payments pay', 'pay.bill_id = b.id', 'left')
            ->where('b.patient_id', $patientId)
            ->groupBy('b.id')
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate totals
        $totalBills = count($bills);
        $totalAmount = array_sum(array_column($bills, 'total_amount'));
        $totalPaid = array_sum(array_column($bills, 'paid_amount'));
        $totalRemaining = $totalAmount - $totalPaid;

        return $this->response->setJSON([
            'patient' => $patient,
            'bills' => $bills,
            'summary' => [
                'total_bills' => $totalBills,
                'total_amount' => $totalAmount,
                'total_paid' => $totalPaid,
                'total_remaining' => $totalRemaining,
            ]
        ]);
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

        $db = db_connect();
        $request = $this->request;

        $expenseType = trim($request->getPost('expense_type') ?? '');
        $category = trim($request->getPost('category') ?? '');
        $description = trim($request->getPost('description') ?? '');
        $amount = floatval($request->getPost('amount') ?? 0);
        $expenseDate = $request->getPost('expense_date') ?: date('Y-m-d');
        $referenceNumber = trim($request->getPost('reference_number') ?? '');
        $paymentMethod = trim($request->getPost('payment_method') ?? 'cash');
        $vendorSupplier = trim($request->getPost('vendor_supplier') ?? '');

        if (!$expenseType || $amount <= 0) {
            session()->setFlashdata('error', 'Please provide expense type and valid amount.');
            return redirect()->to(site_url('accounts/expenses'));
        }

        $userId = session()->get('user_id');

        $data = [
            'expense_type' => $expenseType,
            'category' => $category ?: null,
            'description' => $description ?: null,
            'amount' => $amount,
            'expense_date' => $expenseDate,
            'reference_number' => $referenceNumber ?: null,
            'payment_method' => $paymentMethod,
            'vendor_supplier' => $vendorSupplier ?: null,
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('expenses')->insert($data);

        session()->setFlashdata('success', 'Expense recorded successfully.');
        return redirect()->to(site_url('accounts/expenses'));
    }
}

