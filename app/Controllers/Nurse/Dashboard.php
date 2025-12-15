<?php

namespace App\Controllers\Nurse;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('nurse')) {
            return redirect()->to(site_url('dashboard'));
        }

        $db = db_connect();

        // Count assigned patients (inpatients in admissions)
        $assignedPatients = $db->table('admissions a')
            ->join('patients p', 'p.id = a.patient_id', 'inner')
            ->countAllResults(false);

        // Count pending medications (prescriptions with status 'pending' for inpatients)
        $pendingMedications = $db->table('prescriptions pr')
            ->join('admissions a', 'a.patient_id = pr.patient_id', 'inner')
            ->where('pr.status', 'pending')
            ->countAllResults(false);

        $data = [
            'title' => 'Nurse Dashboard | HMS System',
            'assignedPatients' => $assignedPatients,
            'pendingMedications' => $pendingMedications,
        ];

        return view('nurse/dashboard', $data);
    }
}
