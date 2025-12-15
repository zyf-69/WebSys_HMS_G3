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
            return redirect()->to(site_url('dashboard'));
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

        // Check for flashdata without consuming it (use peekFlashdata or check tempdata)
        $hasSuccess = $this->session->has('success');
        $hasError = $this->session->has('error');
        log_message('info', 'Pharmacy index - Has success flashdata: ' . ($hasSuccess ? 'YES' : 'NO'));
        log_message('info', 'Pharmacy index - Has error flashdata: ' . ($hasError ? 'YES' : 'NO'));

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
            return redirect()->to(site_url('admin/pharmacy'));
        }

        $request = $this->request;

        log_message('info', '=== RESTOCK METHOD CALLED ===');
        log_message('info', 'Request method: ' . $request->getMethod());
        log_message('info', 'Is POST: ' . ($request->is('post') ? 'YES' : 'NO'));

        if (!$request->is('post')) {
            log_message('info', 'Not a POST request, redirecting');
            return redirect()->to(site_url('admin/pharmacy'));
        }

        $medicineId = (int) $request->getPost('medicine_id');
        $quantity = (int) $request->getPost('quantity');
        $supplier = trim((string) $request->getPost('supplier'));
        $notes = trim((string) $request->getPost('notes'));

        log_message('info', sprintf(
            'Restock form data - Medicine ID: %d, Quantity: %d, Supplier: %s',
            $medicineId,
            $quantity,
            $supplier ?: 'N/A'
        ));

        // Validation
        if (!$medicineId || $quantity <= 0) {
            log_message('error', 'Validation failed - Medicine ID: ' . $medicineId . ', Quantity: ' . $quantity);
            $this->session->setFlashdata('error', 'Please select a medicine and enter a valid quantity.');
            return redirect()->to(site_url('admin/pharmacy'));
        }

        $db = db_connect();

        // Check if medicine exists
        $medicine = $db->table('medicines')
            ->where('id', $medicineId)
            ->get()
            ->getRowArray();

        if (!$medicine) {
            log_message('error', 'Medicine not found - ID: ' . $medicineId);
            $this->session->setFlashdata('error', 'Medicine not found.');
            return redirect()->to(site_url('admin/pharmacy'));
        }

        // Calculate new stock quantity
        $previousStock = (int) $medicine['stock_quantity'];
        $newStockQuantity = $previousStock + $quantity;

        // Update stock
        $updateData = [
            'stock_quantity' => $newStockQuantity,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        log_message('info', sprintf(
            'Attempting to update medicine ID %d: Previous Stock: %d, Adding: %d, New Stock: %d',
            $medicineId,
            $previousStock,
            $quantity,
            $newStockQuantity
        ));

        $updateResult = $db->table('medicines')
            ->where('id', $medicineId)
            ->update($updateData);

        $affectedRows = $db->affectedRows();
        log_message('info', 'Update result: ' . ($updateResult ? 'TRUE' : 'FALSE') . ', Affected rows: ' . $affectedRows);

        if ($updateResult === false) {
            $error = $db->error();
            log_message('error', 'Failed to update medicine stock: ' . json_encode($error));
            $this->session->setFlashdata('error', 'Failed to update stock. Please try again.');
            return redirect()->to(site_url('admin/pharmacy'));
        }

        if ($affectedRows === 0) {
            log_message('error', 'No rows affected by update - Medicine ID: ' . $medicineId);
            $this->session->setFlashdata('error', 'No changes were made. The medicine may not exist or stock is already at that value.');
            return redirect()->to(site_url('admin/pharmacy'));
        }

        // Verify the update worked
        $updatedMedicine = $db->table('medicines')
            ->where('id', $medicineId)
            ->get()
            ->getRowArray();

        if (!$updatedMedicine || (int)$updatedMedicine['stock_quantity'] !== $newStockQuantity) {
            log_message('error', sprintf(
                'Stock update verification failed: Expected %d, Got %d',
                $newStockQuantity,
                $updatedMedicine ? (int)$updatedMedicine['stock_quantity'] : 'NULL'
            ));
            $this->session->setFlashdata('error', 'Stock update failed. Please try again.');
            return redirect()->to(site_url('admin/pharmacy'));
        }

        // Log the restock
        log_message('info', sprintf(
            'Medicine restocked successfully: %s (ID: %d) - Added: %d, Previous Stock: %d, New Stock: %d, Supplier: %s',
            $medicine['medicine_name'],
            $medicineId,
            $quantity,
            $previousStock,
            $newStockQuantity,
            $supplier ?: 'N/A'
        ));

        $successMessage = sprintf(
            'Successfully restocked %s. Added %d %s. New stock: %d %s.',
            $medicine['medicine_name'],
            $quantity,
            $medicine['unit'] ?? 'unit',
            $newStockQuantity,
            $medicine['unit'] ?? 'unit'
        );
        
        // Set flashdata (don't call getFlashdata here as it consumes the flashdata)
        $this->session->setFlashdata('success', $successMessage);
        log_message('info', 'Flash success message set: ' . $successMessage);
        log_message('info', 'Session ID: ' . session_id());

        return redirect()->to(site_url('admin/pharmacy'));
    }
}
