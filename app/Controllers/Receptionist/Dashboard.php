<?php

namespace App\Controllers\Receptionist;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('receptionist')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Receptionist Dashboard | HMS System',
        ];

        return view('receptionist/dashboard', $data);
    }
}
