<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class LabResults extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            return redirect()->to(site_url('dashboard'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID from doctors table
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/dashboard'));
        }

        $doctorId = $doctor['id'];

        // Get all lab tests for this doctor's patients
        // Note: lab_tests.doctor_id references doctors.id
        $builder = $db->table('lab_tests');
        $builder->select('lab_tests.*, patients.first_name, patients.middle_name, patients.last_name, patients.id as patient_id');
        $builder->join('patients', 'patients.id = lab_tests.patient_id', 'left');
        $builder->where('lab_tests.doctor_id', $doctorId);
        $builder->orderBy('lab_tests.created_at', 'DESC');
        $labTests = $builder->get()->getResultArray();

        // Calculate statistics
        $totalTests = count($labTests);
        $pendingTests = count(array_filter($labTests, fn($t) => ($t['status'] ?? 'pending') === 'pending'));
        $completedTests = count(array_filter($labTests, fn($t) => ($t['status'] ?? 'pending') === 'completed'));
        $inProgressTests = count(array_filter($labTests, fn($t) => ($t['status'] ?? 'pending') === 'in_progress'));

        // Get patients for dropdown
        $patients = $db->table('patients')
            ->select('id, first_name, middle_name, last_name')
            ->orderBy('last_name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Lab Results | Doctor Panel',
            'labTests' => $labTests,
            'totalTests' => $totalTests,
            'pendingTests' => $pendingTests,
            'completedTests' => $completedTests,
            'inProgressTests' => $inProgressTests,
            'patients' => $patients,
        ];

        return view('doctor/lab_results', $data);
    }

    public function store()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('doctor/lab-results'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID from doctors table
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/lab-results'));
        }

        $doctorId = $doctor['id'];

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('doctor/lab-results'));
        }

        $data = [
            'patient_id' => (int) $request->getPost('patient_id'),
            'doctor_id' => $doctorId, // lab_tests.doctor_id references doctors.id
            'test_type' => trim($request->getPost('test_type') ?? ''),
            'test_name' => trim($request->getPost('test_name') ?? ''),
            'notes' => trim($request->getPost('notes') ?? ''),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Validation
        if (empty($data['patient_id']) || empty($data['test_type']) || empty($data['test_name'])) {
            $this->session->setFlashdata('error', 'Please fill in all required fields.');
            return redirect()->to(site_url('doctor/lab-results'));
        }

        try {
            $db->table('lab_tests')->insert($data);
            $this->session->setFlashdata('success', 'Lab request created successfully.');
            return redirect()->to(site_url('doctor/lab-results'));
        } catch (\Exception $e) {
            log_message('error', 'Lab request creation failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Failed to create lab request. Please try again.');
            return redirect()->to(site_url('doctor/lab-results'));
        }
    }
}

