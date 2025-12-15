<?php

namespace App\Controllers\It;

use App\Controllers\BaseController;

class SystemMaintenance extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!($this->hasRole('it_staff') || $this->hasRole('it'))) {
            return redirect()->to(site_url('dashboard'));
        }

        $data = [
            'title' => 'System Maintenance | IT Panel',
        ];

        return view('it/system_maintenance', $data);
    }
}

