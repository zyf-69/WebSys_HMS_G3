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

        $data = [
            'title' => 'Laboratory Dashboard | HMS System',
        ];

        return view('lab/dashboard', $data);
    }
}
