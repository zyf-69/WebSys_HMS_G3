<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Laboratory extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Get all lab tests with patient and doctor information
        $builder = $db->table('lab_tests');
        $builder->select('lab_tests.*, patients.first_name, patients.middle_name, patients.last_name, users.first_name as doctor_first, users.last_name as doctor_last');
        $builder->join('patients', 'patients.id = lab_tests.patient_id', 'left');
        $builder->join('users', 'users.id = lab_tests.doctor_id', 'left');
        $builder->orderBy('lab_tests.created_at', 'DESC');
        $tests = $builder->get()->getResultArray();
        
        // Format doctor name
        foreach ($tests as &$test) {
            $test['doctor_name'] = trim(($test['doctor_first'] ?? '') . ' ' . ($test['doctor_last'] ?? ''));
        }
        
        // Calculate statistics
        $totalTests = count($tests);
        $pendingTests = count(array_filter($tests, fn($t) => ($t['status'] ?? 'pending') === 'pending'));
        $completedTests = count(array_filter($tests, fn($t) => ($t['status'] ?? 'pending') === 'completed'));
        
        // Get patients and doctors for dropdowns
        $patients = $db->table('patients')
            ->select('id, first_name, middle_name, last_name')
            ->orderBy('last_name', 'ASC')
            ->get()
            ->getResultArray();
        
        // Get doctor role ID
        $doctorRole = $db->table('roles')
            ->where('name', 'doctor')
            ->get()
            ->getRowArray();
        
        $doctors = [];
        if ($doctorRole) {
            $doctors = $db->table('users')
                ->select('users.id, users.first_name, users.last_name, users.middle_name')
                ->where('role_id', $doctorRole['id'])
                ->where('status', 'active')
                ->get()
                ->getResultArray();
        }
        
        foreach ($doctors as &$doctor) {
            $doctor['full_name'] = trim(($doctor['first_name'] ?? '') . ' ' . ($doctor['middle_name'] ?? '') . ' ' . ($doctor['last_name'] ?? ''));
        }
        
        return view('admin/laboratory', [
            'tests' => $tests,
            'totalTests' => $totalTests,
            'pendingTests' => $pendingTests,
            'completedTests' => $completedTests,
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }
    
    public function store()
    {
        $db = \Config\Database::connect();
        
        $doctorId = $this->request->getPost('doctor_id');
        if (empty($doctorId)) {
            $doctorId = null;
        }
        
        $data = [
            'patient_id' => $this->request->getPost('patient_id'),
            'doctor_id' => $doctorId,
            'test_type' => $this->request->getPost('test_type'),
            'test_name' => $this->request->getPost('test_name'),
            'notes' => $this->request->getPost('notes'),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        if (empty($data['patient_id']) || empty($data['test_type']) || empty($data['test_name'])) {
            return redirect()->to('/WebSys_HMS_G3/admin/laboratory')->with('error', 'Please fill in all required fields.');
        }
        
        try {
            $db->table('lab_tests')->insert($data);
            return redirect()->to('/WebSys_HMS_G3/admin/laboratory')->with('success', 'Lab test created successfully.');
        } catch (\Exception $e) {
            log_message('error', 'Lab test creation failed: ' . $e->getMessage());
            return redirect()->to('/WebSys_HMS_G3/admin/laboratory')->with('error', 'Failed to create lab test. Please try again.');
        }
    }
    
    public function updateStatus($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }
        
        if (!$this->hasRole(['admin', 'hospital_administrator', 'lab_staff'])) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('admin/laboratory'));
        }
        
        $request = $this->request;
        $db = \Config\Database::connect();
        
        $status = trim($request->getPost('status') ?? '');
        if (!in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'])) {
            $this->session->setFlashdata('error', 'Invalid status.');
            return redirect()->to(site_url('admin/laboratory'));
        }
        
        // Get lab test details
        $labTest = $db->table('lab_tests')
            ->where('id', $id)
            ->get()
            ->getRowArray();
        
        if (!$labTest) {
            $this->session->setFlashdata('error', 'Lab test not found.');
            return redirect()->to(site_url('admin/laboratory'));
        }
        
        $db->transStart();
        
        try {
            // Update lab test status
            $db->table('lab_tests')
                ->where('id', $id)
                ->update([
                    'status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            
            // Auto-generate bill when lab test is marked as completed
            if ($status === 'completed' && ($labTest['status'] ?? '') !== 'completed') {
                // Get test price from configuration
                $testPrices = config('TestPrices');
                $testType = trim($labTest['test_type'] ?? '');
                $testName = trim($labTest['test_name'] ?? '');
                $testAmount = $testPrices->getPrice($testType, $testName);
                
                $description = $testName ?: ($labTest['test_name'] ?? 'Lab Test') . ' (' . $testType . ')';
                $this->generateBill($labTest['patient_id'], 'Laboratory', $testAmount, $description);
            }
            
            if ($db->transComplete() === false) {
                throw new \Exception('Transaction failed.');
            }
            
            $this->session->setFlashdata('success', 'Lab test status updated successfully.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Lab test status update failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Failed to update lab test status.');
        }
        
        return redirect()->to(site_url('admin/laboratory'));
    }
    
    protected function generateBill($patientId, $billType, $amount, $description = null)
    {
        if (!$patientId || !$billType || $amount <= 0) {
            log_message('error', 'Invalid parameters for bill generation: patient_id=' . $patientId . ', bill_type=' . $billType . ', amount=' . $amount);
            return false;
        }
        
        $db = \Config\Database::connect();
        
        try {
            // Generate invoice number: INV-YYYY-NNNN
            $year = date('Y');
            $lastInvoice = $db->table('bills')
                ->like('invoice_number', "INV-{$year}-", 'after')
                ->orderBy('invoice_number', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();
            
            if ($lastInvoice && !empty($lastInvoice['invoice_number'])) {
                $parts = explode('-', $lastInvoice['invoice_number']);
                $sequence = isset($parts[2]) ? (int) $parts[2] : 0;
                $sequence++;
            } else {
                $sequence = 1;
            }
            
            $invoiceNumber = sprintf('INV-%s-%04d', $year, $sequence);
            
            $billData = [
                'patient_id' => $patientId,
                'invoice_number' => $invoiceNumber,
                'bill_type' => $billType,
                'total_amount' => $amount,
                'description' => $description,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            
            $db->table('bills')->insert($billData);
            return $db->insertID();
        } catch (\Exception $e) {
            log_message('error', 'Failed to generate bill: ' . $e->getMessage());
            return false;
        }
    }
}

