<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Pharmacy extends BaseController
{
    public function index()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to access this page.');
            return redirect()->to(base_url('dashboard'));
        }

        $db = db_connect();

        // Get all medicines with calculated total value
        $medicines = $db->table('medicines')
            ->select('id, medicine_name, category, stock_quantity, unit_price, unit, status, created_at, updated_at')
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

        // Calculate summary statistics
        $totalMedicines = count($medicines);
        $outOfStockCount = count(array_filter($medicines, function($m) { return $m['is_out_of_stock']; }));
        $lowStockCount = count(array_filter($medicines, function($m) { return $m['is_low_stock']; }));
        $totalInventoryValue = array_sum(array_column($medicines, 'total_value'));

        $data = [
            'title' => 'Pharmacy Inventory | Admin Panel',
            'medicines' => $medicines,
            'totalMedicines' => $totalMedicines,
            'outOfStockCount' => $outOfStockCount,
            'lowStockCount' => $lowStockCount,
            'totalInventoryValue' => $totalInventoryValue,
        ];

        return view('admin/pharmacy', $data);
    }

    public function restock()
    {
        $result = $this->requireLogin();
        if ($result !== true) {
            return $result;
        }

        if (!$this->hasRole(['admin', 'hospital_administrator'])) {
            $this->session->setFlashdata('error', 'You do not have permission to perform this action.');
            return redirect()->to(base_url('admin/pharmacy'));
        }

        $request = $this->request;

        if (!$request->is('post')) {
            return redirect()->to(base_url('admin/pharmacy'));
        }

        $medicineId = (int) $request->getPost('medicine_id');
        $quantity = (int) $request->getPost('quantity');
        $supplier = trim((string) $request->getPost('supplier'));
        $notes = trim((string) $request->getPost('notes'));

        // Validation
        if (!$medicineId || $quantity <= 0) {
            $this->session->setFlashdata('error', 'Please select a medicine and enter a valid quantity.');
            return redirect()->to(base_url('admin/pharmacy'));
        }

        $db = db_connect();

        // Check if medicine exists
        $medicine = $db->table('medicines')
            ->where('id', $medicineId)
            ->get()
            ->getRowArray();

        if (!$medicine) {
            $this->session->setFlashdata('error', 'Medicine not found.');
            return redirect()->to(base_url('admin/pharmacy'));
        }

        // Calculate new stock quantity
        $newStockQuantity = $medicine['stock_quantity'] + $quantity;

        // Update stock
        $updateData = [
            'stock_quantity' => $newStockQuantity,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db->table('medicines')
            ->where('id', $medicineId)
            ->update($updateData);

        // Log the restock (optional: you can create a restock_logs table later)
        log_message('info', sprintf(
            'Medicine restocked: %s (ID: %d) - Added: %d, Previous Stock: %d, New Stock: %d, Supplier: %s',
            $medicine['medicine_name'],
            $medicineId,
            $quantity,
            $medicine['stock_quantity'],
            $newStockQuantity,
            $supplier ?: 'N/A'
        ));

        $this->session->setFlashdata('success', sprintf(
            'Successfully restocked %s. Added %d %s. New stock: %d %s.',
            $medicine['medicine_name'],
            $quantity,
            $medicine['unit'] ?? 'unit',
            $newStockQuantity,
            $medicine['unit'] ?? 'unit'
        ));

        return redirect()->to(base_url('admin/pharmacy'));
    }
}
