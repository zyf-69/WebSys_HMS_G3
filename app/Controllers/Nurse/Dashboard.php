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
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Nurse Dashboard | HMS System',
        ];

        return view('nurse/dashboard', $data);
    }
}
