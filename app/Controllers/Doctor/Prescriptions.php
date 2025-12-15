<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class Prescriptions extends BaseController
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

        // Get all prescriptions for this doctor
        $builder = $db->table('prescriptions');
        $builder->select('prescriptions.*, patients.first_name, patients.middle_name, patients.last_name, patients.id as patient_id, medicines.medicine_name, medicines.unit');
        $builder->join('patients', 'patients.id = prescriptions.patient_id', 'left');
        $builder->join('medicines', 'medicines.id = prescriptions.medicine_id', 'left');
        $builder->where('prescriptions.doctor_id', $doctorId);
        $builder->orderBy('prescriptions.created_at', 'DESC');
        $prescriptions = $builder->get()->getResultArray();

        // Calculate statistics
        $totalPrescriptions = count($prescriptions);
        $pendingPrescriptions = count(array_filter($prescriptions, fn($p) => ($p['status'] ?? 'pending') === 'pending'));
        $dispensedPrescriptions = count(array_filter($prescriptions, fn($p) => ($p['status'] ?? 'pending') === 'dispensed'));
        $partiallyDispensed = count(array_filter($prescriptions, fn($p) => ($p['status'] ?? 'pending') === 'partially_dispensed'));

        // Get patients and medicines for dropdowns
        $patients = $db->table('patients')
            ->select('id, first_name, middle_name, last_name')
            ->orderBy('last_name', 'ASC')
            ->get()
            ->getResultArray();

        $medicines = $db->table('medicines')
            ->select('id, medicine_name, unit, stock_quantity')
            ->where('status', 'active')
            ->orderBy('medicine_name', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title' => 'Prescriptions | Doctor Panel',
            'prescriptions' => $prescriptions,
            'totalPrescriptions' => $totalPrescriptions,
            'pendingPrescriptions' => $pendingPrescriptions,
            'dispensedPrescriptions' => $dispensedPrescriptions,
            'partiallyDispensed' => $partiallyDispensed,
            'patients' => $patients,
            'medicines' => $medicines,
        ];

        return view('doctor/prescriptions', $data);
    }

    public function store()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('doctor')) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(site_url('doctor/prescriptions'));
        }

        $userId = session()->get('user_id');
        $db = db_connect();

        // Get doctor ID
        $doctor = $db->table('doctors')
            ->where('user_id', $userId)
            ->get()
            ->getRowArray();

        if (!$doctor) {
            $this->session->setFlashdata('error', 'Doctor profile not found.');
            return redirect()->to(site_url('doctor/prescriptions'));
        }

        $doctorId = $doctor['id'];

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(site_url('doctor/prescriptions'));
        }

        $data = [
            'patient_id' => (int) $request->getPost('patient_id'),
            'doctor_id' => $doctorId,
            'medicine_id' => (int) $request->getPost('medicine_id'),
            'prescribed_quantity' => (int) $request->getPost('prescribed_quantity'),
            'prescription_date' => $request->getPost('prescription_date') ?: date('Y-m-d'),
            'notes' => trim($request->getPost('notes') ?? ''),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Validation
        if (empty($data['patient_id']) || empty($data['medicine_id']) || $data['prescribed_quantity'] <= 0) {
            $this->session->setFlashdata('error', 'Please fill in all required fields.');
            return redirect()->to(site_url('doctor/prescriptions'));
        }

        try {
            $db->table('prescriptions')->insert($data);
            $this->session->setFlashdata('success', 'Prescription created successfully.');
            return redirect()->to(site_url('doctor/prescriptions'));
        } catch (\Exception $e) {
            log_message('error', 'Prescription creation failed: ' . $e->getMessage());
            $this->session->setFlashdata('error', 'Failed to create prescription. Please try again.');
            return redirect()->to(site_url('doctor/prescriptions'));
        }
    }
}

