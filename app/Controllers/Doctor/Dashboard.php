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
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Doctor Dashboard | HMS System',
        ];

        return view('doctor/dashboard', $data);
    }
}
