<?php

namespace App\Controllers\Lab;

use App\Controllers\BaseController;

class TestRequests extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('lab_staff') || $this->hasRole('laboratory_staff'))) {
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();

        // Get all lab tests with patient and doctor information
        $tests = $db->table('lab_tests lt')
            ->select('lt.*, 
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                p.id as patient_id,
                d.full_name as doctor_name')
            ->join('patients p', 'p.id = lt.patient_id', 'left')
            ->join('doctors d', 'd.id = lt.doctor_id', 'left')
            ->orderBy('lt.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate statistics
        $totalTests = count($tests);
        $pendingTests = count(array_filter($tests, fn($t) => ($t['status'] ?? 'pending') === 'pending'));
        $inProgressTests = count(array_filter($tests, fn($t) => ($t['status'] ?? 'pending') === 'in_progress'));
        $completedTests = count(array_filter($tests, fn($t) => ($t['status'] ?? 'pending') === 'completed'));

        $data = [
            'title' => 'Test Requests | Laboratory Panel',
            'tests' => $tests,
            'totalTests' => $totalTests,
            'pendingTests' => $pendingTests,
            'inProgressTests' => $inProgressTests,
            'completedTests' => $completedTests,
        ];

        return view('lab/test_requests', $data);
    }

    public function updateStatus($id)
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('lab_staff') || $this->hasRole('laboratory_staff'))) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('lab/test-requests'));
        }

        $request = $this->request;
        $db = db_connect();

        $status = trim($request->getPost('status') ?? '');
        if (!in_array($status, ['pending', 'in_progress', 'completed', 'cancelled'])) {
            $this->session->setFlashdata('error', 'Invalid status.');
            return redirect()->to(site_url('lab/test-requests'));
        }

        // Get lab test details
        $labTest = $db->table('lab_tests')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$labTest) {
            $this->session->setFlashdata('error', 'Lab test not found.');
            return redirect()->to(site_url('lab/test-requests'));
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

            $this->session->setFlashdata('success', 'Test status updated successfully.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Lab test status update failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Failed to update test status.');
        }

        return redirect()->to(site_url('lab/test-requests'));
    }
}

