<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;

class FollowUp extends BaseController
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

        // Get all follow-ups with patient and doctor information
        $followUps = $db->table('follow_ups fu')
            ->select('fu.*, 
                p.id as patient_id, p.first_name, p.middle_name, p.last_name, p.patient_code,
                d.full_name as doctor_name, d.specialization,
                a.appointment_date as original_appointment_date')
            ->join('patients p', 'p.id = fu.patient_id', 'left')
            ->join('doctors d', 'd.id = fu.doctor_id', 'left')
            ->join('appointments a', 'a.id = fu.original_appointment_id', 'left')
            ->orderBy('fu.follow_up_date', 'ASC')
            ->orderBy('fu.follow_up_time', 'ASC')
            ->get()
            ->getResultArray();

        // Format patient names
        foreach ($followUps as &$followUp) {
            $followUp['patient_name'] = trim(($followUp['first_name'] ?? '') . ' ' . ($followUp['middle_name'] ?? '') . ' ' . ($followUp['last_name'] ?? ''));
        }
        unset($followUp);

        // Calculate statistics
        $totalFollowUps = count($followUps);
        $scheduledFollowUps = count(array_filter($followUps, fn($f) => ($f['status'] ?? 'scheduled') === 'scheduled'));
        $completedFollowUps = count(array_filter($followUps, fn($f) => ($f['status'] ?? '') === 'completed'));
        $upcomingToday = count(array_filter($followUps, fn($f) => ($f['status'] ?? '') === 'scheduled' && ($f['follow_up_date'] ?? '') === date('Y-m-d')));

        $data = [
            'title' => 'Follow-up Management | Receptionist Panel',
            'followUps' => $followUps,
            'totalFollowUps' => $totalFollowUps,
            'scheduledFollowUps' => $scheduledFollowUps,
            'completedFollowUps' => $completedFollowUps,
            'upcomingToday' => $upcomingToday,
        ];

        return view('receptionist/follow_up', $data);
    }

}

