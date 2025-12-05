<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('pharmacist')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Pharmacy Dashboard | HMS System',
        ];

        return view('pharmacy/dashboard', $data);
    }
}
