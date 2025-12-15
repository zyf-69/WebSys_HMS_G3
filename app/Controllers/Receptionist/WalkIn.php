<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;

class WalkIn extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('receptionist')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Get all outpatients (patients without admission records)
        $outpatients = $db->table('patients p')
            ->select('p.id, p.patient_code, p.first_name, p.middle_name, p.last_name, p.date_of_birth, p.gender, p.created_at,
                pc.phone_number,
                d.full_name as doctor_name, d.specialization')
            ->join('patient_contacts pc', 'pc.patient_id = p.id', 'left')
            ->join('doctors d', 'd.id = p.doctor_id', 'left')
            ->join('admissions a', 'a.patient_id = p.id', 'left')
            ->where('a.id IS NULL', null, false) // Only outpatients
            ->orderBy('p.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Format patient data
        foreach ($outpatients as &$patient) {
            $patient['full_name'] = trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
            if ($patient['date_of_birth']) {
                $birthDate = new \DateTime($patient['date_of_birth']);
                $today = new \DateTime();
                $patient['age'] = $today->diff($birthDate)->y;
            } else {
                $patient['age'] = null;
            }
            // Extract test type from phone_number field
            if (!empty($patient['phone_number']) && strpos($patient['phone_number'], 'TEST_TYPE:') === 0) {
                $patient['test_type'] = str_replace('TEST_TYPE:', '', $patient['phone_number']);
                $patient['phone_number'] = null; // Clear it since it's not actually a phone number
            } else {
                $patient['test_type'] = null;
            }
        }
        unset($patient);

        // Calculate statistics
        $totalWalkIns = count($outpatients);
        $todayWalkIns = count(array_filter($outpatients, function($p) {
            $createdDate = $p['created_at'] ?? null;
            return $createdDate && date('Y-m-d', strtotime($createdDate)) === date('Y-m-d');
        }));

        // Get doctors for dropdown
        $role = $db->table('roles')->where('name', 'doctor')->get()->getRowArray();
        $roleId = $role['id'] ?? null;
        
        if ($roleId) {
            $doctors = $db->table('doctors d')
                ->select('d.id, d.full_name, d.specialization')
                ->join('users u', 'u.id = d.user_id', 'inner')
                ->where('u.role_id', $roleId)
                ->orderBy('d.full_name', 'ASC')
                ->get()
                ->getResultArray();
        } else {
            $doctors = [];
        }

        $data = [
            'title' => 'Walk-in Management | Receptionist Panel',
            'outpatients' => $outpatients,
            'totalWalkIns' => $totalWalkIns,
            'todayWalkIns' => $todayWalkIns,
            'doctors' => $doctors,
        ];

        return view('receptionist/walk_in', $data);
    }

    public function store()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('receptionist')) {
            session()->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('receptionist/walk-in'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('receptionist/walk-in'));
        }

        $db = db_connect();
        $db->transStart();

        try {
            // Core patient data
            $patientData = [
                'first_name' => trim($request->getPost('first_name') ?? ''),
                'middle_name' => trim($request->getPost('middle_name') ?? ''),
                'last_name' => trim($request->getPost('last_name') ?? ''),
                'date_of_birth' => $request->getPost('date_of_birth') ?: null,
                'gender' => $request->getPost('gender') ?: null,
                'doctor_id' => $request->getPost('doctor_id') ? (int) $request->getPost('doctor_id') : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Validation
            if (empty($patientData['first_name']) || empty($patientData['last_name'])) {
                session()->setFlashdata('error', 'First name and last name are required.');
                $db->transRollback();
                return redirect()->to(site_url('receptionist/walk-in'));
            }

            $testType = trim($request->getPost('test_type') ?? '');
            if (empty($testType)) {
                session()->setFlashdata('error', 'Test type is required.');
                $db->transRollback();
                return redirect()->to(site_url('receptionist/walk-in'));
            }

            $db->table('patients')->insert($patientData);
            $patientId = $db->insertID();

            // Store test type in patient_contacts (using a field as storage for test_type)
            $testType = trim($request->getPost('test_type') ?? '');
            if (!empty($testType)) {
                // Store test type in patient_contacts for easy retrieval
                // Using phone_number field as a temporary storage for test_type
                $contactData = [
                    'patient_id' => $patientId,
                    'phone_number' => 'TEST_TYPE:' . $testType, // Store test type here
                ];
                $db->table('patient_contacts')->insert($contactData);
            }

            if ($db->transComplete() === false) {
                session()->setFlashdata('error', 'Failed to create walk-in request. Please try again.');
                return redirect()->to(site_url('receptionist/walk-in'));
            }

            session()->setFlashdata('success', 'Walk-in patient request created successfully.');
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Walk-in request creation failed: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to create walk-in request. Please try again.');
        }

        return redirect()->to(site_url('receptionist/walk-in'));
    }
}

