<?php

namespace App\Controllers\It;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'IT Dashboard | HMS System',
        ];

        return view('it/dashboard', $data);
    }
}
