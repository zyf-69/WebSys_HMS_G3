<?php

namespace App\Controllers\Accounts;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('accountant')) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Accounts Dashboard | HMS System',
        ];

        return view('accounts/dashboard', $data);
    }
}
