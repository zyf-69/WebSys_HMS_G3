<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

class Inventory extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole('pharmacist')) {
            $this->session->setFlashdata('error', 'You do not have permission to access this page.');
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();

        // Get all medicines for inventory
        $medicines = $db->table('medicines')
            ->select('id, medicine_name, category, stock_quantity, unit_price, unit, status, created_at, updated_at')
            ->where('status', 'active')
            ->orderBy('medicine_name', 'ASC')
            ->get()
            ->getResultArray();

        // Calculate total value for each medicine
        foreach ($medicines as &$medicine) {
            $medicine['total_value'] = (float) $medicine['stock_quantity'] * (float) $medicine['unit_price'];
            $medicine['is_out_of_stock'] = $medicine['stock_quantity'] <= 0;
            $medicine['is_low_stock'] = $medicine['stock_quantity'] > 0 && $medicine['stock_quantity'] < 10;
        }
        unset($medicine);

        // Calculate statistics for medicines
        $totalMedicines = count($medicines);
        $outOfStockCount = count(array_filter($medicines, function($m) { return $m['is_out_of_stock']; }));
        $lowStockCount = count(array_filter($medicines, function($m) { return $m['is_low_stock']; }));
        $totalInventoryValue = array_sum(array_column($medicines, 'total_value'));

        // Get unique categories for filter
        $categories = array_unique(array_column($medicines, 'category'));
        sort($categories);

        $data = [
            'title' => 'Medicine Inventory | Pharmacy Panel',
            'medicines' => $medicines,
            'totalMedicines' => $totalMedicines,
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
            'totalInventoryValue' => $totalInventoryValue,
            'categories' => $categories,
        ];

        return view('pharmacy/inventory', $data);
    }
}

