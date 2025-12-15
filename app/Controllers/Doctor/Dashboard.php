<?php

namespace App\Controllers\Doctor;

use App\Controllers\BaseController;

class Dashboard extends BaseController
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
            return redirect()->to(site_url('dashboard'));
        }

        $doctorId = $doctor['id'];
        $today = date('Y-m-d');

        // Count patients with appointments today
        $patientsToday = $db->table('appointments a')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('ds.doctor_id', $doctorId)
            ->where('a.appointment_date', $today)
            ->whereIn('a.status', ['scheduled', 'pending', 'confirmed'])
            ->countAllResults(false);

        // Count pending lab requests (lab tests assigned to this doctor)
        $pendingLabRequests = $db->table('lab_tests')
            ->where('doctor_id', $userId)
            ->where('status', 'pending')
            ->countAllResults(false);

        // Count new messages (for now, we'll use appointments as placeholder)
        // In a real system, this would be from a messages/notifications table
        $newMessages = $db->table('appointments a')
            ->join('doctor_schedules ds', 'ds.id = a.doctor_schedule_id', 'inner')
            ->where('ds.doctor_id', $doctorId)
            ->where('a.appointment_date >=', $today)
            ->where('a.status', 'pending')
            ->countAllResults(false);

        $data = [
            'title' => 'Doctor Dashboard | HMS System',
            'patientsToday' => $patientsToday,
            'pendingLabRequests' => $pendingLabRequests,
            'newMessages' => $newMessages,
        ];

        return view('doctor/dashboard', $data);
    }
}
