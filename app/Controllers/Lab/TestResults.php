<?php

namespace App\Controllers\Lab;

use App\Controllers\BaseController;

class TestResults extends BaseController
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

        // Get completed lab tests with patient and doctor information
        $tests = $db->table('lab_tests lt')
            ->select('lt.*, 
                CONCAT(p.first_name, " ", COALESCE(p.middle_name, ""), " ", p.last_name) as patient_name,
                p.id as patient_id,
                d.full_name as doctor_name')
            ->join('patients p', 'p.id = lt.patient_id', 'left')
            ->join('doctors d', 'd.id = lt.doctor_id', 'left')
            ->where('lt.status', 'completed')
            ->orderBy('lt.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        // Calculate statistics
        $totalCompleted = count($tests);
        $todayCompleted = count(array_filter($tests, function($t) {
            return date('Y-m-d', strtotime($t['updated_at'] ?? '')) === date('Y-m-d');
        }));

        // Group by test type
        $byTestType = [];
        foreach ($tests as $test) {
            $type = $test['test_type'] ?? 'Other';
            if (!isset($byTestType[$type])) {
                $byTestType[$type] = 0;
            }
            $byTestType[$type]++;
        }

        $data = [
            'title' => 'Test Results | Laboratory Panel',
            'tests' => $tests,
            'totalCompleted' => $totalCompleted,
            'todayCompleted' => $todayCompleted,
            'byTestType' => $byTestType,
        ];

        return view('lab/test_results', $data);
    }
}

