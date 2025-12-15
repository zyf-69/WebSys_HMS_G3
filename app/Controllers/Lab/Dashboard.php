<?php

namespace App\Controllers\Lab;

use App\Controllers\BaseController;

class Dashboard extends BaseController
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

        // Get statistics
        $pendingTests = $db->table('lab_tests')
            ->where('status', 'pending')
            ->countAllResults();

        $todayCompleted = $db->table('lab_tests')
            ->where('status', 'completed')
            ->where('DATE(updated_at)', date('Y-m-d'))
            ->countAllResults();

        $inProgressTests = $db->table('lab_tests')
            ->where('status', 'in_progress')
            ->countAllResults();

        $totalTests = $db->table('lab_tests')
            ->countAllResults();

        $data = [
            'title' => 'Laboratory Dashboard | HMS System',
            'pendingTests' => $pendingTests,
            'todayCompleted' => $todayCompleted,
            'inProgressTests' => $inProgressTests,
            'totalTests' => $totalTests,
        ];

        return view('lab/dashboard', $data);
    }
}
