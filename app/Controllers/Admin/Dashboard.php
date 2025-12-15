<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('admin')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Admin Dashboard | HMS System',
        ];

        return view('admin/dashboard', $data);
    }
}
