<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Pharmacy Dashboard</div>
        <div class="page-subtitle">Manage inventory and fulfill prescriptions.</div>
    </div>
</div>

<style>
    .dashboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
    }
    .stat-label {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }
    .stat-value.warning {
        color: #d97706;
    }
    .stat-value.danger {
        color: #dc2626;
    }
    .stat-value.success {
        color: #16a34a;
    }
    .section-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 24px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f3f4f6;
    }
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .data-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .data-table th {
        text-align: left;
        padding: 12px;
        font-weight: 600;
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
    }
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    .data-table tbody tr:hover {
        background: #f9fafb;
    }
    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .badge-category {
        background: #e0e7ff;
        color: #4338ca;
    }
    .badge-stock-out {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-stock-low {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-stock-ok {
        background: #dcfce7;
        color: #166534;
    }
    .badge-status-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-status-partial {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-status-dispensed {
        background: #dcfce7;
        color: #166534;
    }
    .badge-status-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    .btn-dispense {
        background: #16a34a;
        color: #ffffff;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-dispense:hover {
        background: #15803d;
    }
    .btn-dispense:disabled {
        background: #d1d5db;
        color: #9ca3af;
        cursor: not-allowed;
    }
    .stock-warning {
        color: #dc2626;
        font-weight: 600;
        font-size: 12px;
    }
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-content {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        width: 90%;
        max-width: 500px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }
    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
    }
    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        color: #6b7280;
        cursor: pointer;
        padding: 0;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
    }
    .modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        transition: border-color 0.2s;
    }
    .form-input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    .btn-primary {
        flex: 1;
        background: #16a34a;
        color: #ffffff;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: #15803d;
    }
    .btn-secondary {
        flex: 1;
        background: #f3f4f6;
        color: #374151;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    .info-text {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
</style>

<?php if (session()->getFlashdata('error')): ?>
    <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 16px; font-size: 13px;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="dashboard-stats">
    <div class="stat-card">
        <div class="stat-label">Total Medicines</div>
        <div class="stat-value"><?= number_format($totalMedicines ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Out of Stock</div>
        <div class="stat-value danger"><?= number_format($outOfStockCount ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Low Stock</div>
        <div class="stat-value warning"><?= number_format($lowStockCount ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Prescriptions</div>
        <div class="stat-value warning"><?= number_format($pendingPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partially Dispensed</div>
        <div class="stat-value"><?= number_format($partiallyDispensed ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Inventory Value</div>
        <div class="stat-value success">₱<?= number_format($totalInventoryValue ?? 0, 2) ?></div>
    </div>
</div>

<!-- Inventory Section -->
<div class="section-card">
    <div class="section-header">
        <div class="section-title">📦 Medicine Inventory</div>
    </div>

    <div class="search-filter" style="display: flex; gap: 12px; margin-bottom: 16px;">
        <input type="text" class="search-input" id="inventorySearchInput" placeholder="Search by medicine name or category..." style="flex: 1; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px;">
        <select class="filter-select" id="inventoryCategoryFilter" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #ffffff;">
            <option value="">All Categories</option>
            <option value="Antibiotic">Antibiotic</option>
            <option value="Pain Relief">Pain Relief</option>
            <option value="Cardiovascular">Cardiovascular</option>
            <option value="Antidiabetic">Antidiabetic</option>
            <option value="Respiratory">Respiratory</option>
            <option value="Gastrointestinal">Gastrointestinal</option>
            <option value="Vitamins">Vitamins</option>
            <option value="Other">Other</option>
        </select>
        <select class="filter-select" id="inventoryStockFilter" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; background: #ffffff;">
            <option value="">All Stock Status</option>
            <option value="out">Out of Stock</option>
            <option value="low">Low Stock</option>
            <option value="in">In Stock</option>
        </select>
    </div>

    <?php if (empty($medicines)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">💊</div>
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">No Medicines Available</div>
            <div>No medicines have been added to the inventory yet.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table" id="inventoryTable">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Stock Quantity</th>
                        <th>Unit</th>
                        <th>Unit Price</th>
                        <th>Total Value</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    <?php foreach ($medicines as $medicine): ?>
                        <tr data-search="<?= strtolower(esc($medicine['medicine_name'] . ' ' . ($medicine['category'] ?? '') . ' ' . ($medicine['unit'] ?? ''))) ?>" 
                            data-category="<?= esc($medicine['category'] ?? '') ?>"
                            data-stock-status="<?= $medicine['is_out_of_stock'] ? 'out' : ($medicine['is_low_stock'] ? 'low' : 'in') ?>">
                            <td>
                                <div style="font-weight: 600; color: #111827;"><?= esc($medicine['medicine_name']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($medicine['category'])): ?>
                                    <span class="badge badge-category"><?= esc($medicine['category']) ?></span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $stockClass = 'badge-stock-out';
                                if ($medicine['stock_quantity'] > 0) {
                                    if ($medicine['stock_quantity'] < 10) {
                                        $stockClass = 'badge-stock-low';
                                    } else {
                                        $stockClass = 'badge-stock-ok';
                                    }
                                }
                                ?>
                                <span class="badge <?= $stockClass ?>">
                                    <?= number_format($medicine['stock_quantity']) ?>
                                </span>
                            </td>
                            <td><?= esc($medicine['unit'] ?? 'unit') ?></td>
                            <td>
                                <span style="font-weight: 600; color: #111827;">₱<?= number_format($medicine['unit_price'], 2) ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #16a34a;">₱<?= number_format($medicine['total_value'] ?? 0, 2) ?></span>
                            </td>
                            <td>
                                <?php if ($medicine['is_out_of_stock']): ?>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #fee2e2; color: #991b1b;">
                                        Out of Stock
                                    </span>
                                <?php elseif ($medicine['is_low_stock']): ?>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #fef3c7; color: #92400e;">
                                        Low Stock
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; background: #dcfce7; color: #166534;">
                                        Available
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Prescriptions Section -->
<div class="section-card">
    <div class="section-header">
        <div class="section-title">💊 Prescriptions</div>
    </div>

    <?php if (empty($prescriptions)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">No Prescriptions</div>
            <div>No prescriptions have been created yet.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Doctor Name</th>
                        <th>Medicine</th>
                        <th>Prescribed Qty</th>
                        <th>Dispensed Qty</th>
                        <th>Remaining Qty</th>
                        <th>Available Stock</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #111827;"><?= esc($prescription['patient_name']) ?></div>
                            </td>
                            <td>
                                <div><?= esc($prescription['doctor_name'] ?? 'Unknown') ?></div>
                                <?php if (!empty($prescription['specialization'])): ?>
                                    <div style="font-size: 11px; color: #6b7280;"><?= esc($prescription['specialization']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #111827;"><?= esc($prescription['medicine_name'] ?? 'Unknown') ?></div>
                                <?php if (!empty($prescription['medicine_category'])): ?>
                                    <div style="font-size: 11px; color: #6b7280;"><?= esc($prescription['medicine_category']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600;"><?= number_format($prescription['prescribed_quantity']) ?></span>
                                <span style="font-size: 11px; color: #6b7280;"><?= esc($prescription['medicine_unit'] ?? 'unit') ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #16a34a;"><?= number_format($prescription['dispensed_quantity']) ?></span>
                                <span style="font-size: 11px; color: #6b7280;"><?= esc($prescription['medicine_unit'] ?? 'unit') ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600;"><?= number_format($prescription['remaining_quantity']) ?></span>
                                <span style="font-size: 11px; color: #6b7280;"><?= esc($prescription['medicine_unit'] ?? 'unit') ?></span>
                            </td>
                            <td>
                                <?php if ($prescription['medicine_stock'] < $prescription['remaining_quantity']): ?>
                                    <span class="stock-warning">
                                        <?= number_format($prescription['medicine_stock']) ?> 
                                        (Shortage: <?= number_format($prescription['stock_shortage']) ?>)
                                    </span>
                                <?php else: ?>
                                    <span style="font-weight: 600; color: #16a34a;">
                                        <?= number_format($prescription['medicine_stock']) ?>
                                    </span>
                                <?php endif; ?>
                                <span style="font-size: 11px; color: #6b7280;"><?= esc($prescription['medicine_unit'] ?? 'unit') ?></span>
                            </td>
                            <td>
                                <?php
                                $statusClass = 'badge-status-pending';
                                $statusText = 'Pending';
                                switch ($prescription['status']) {
                                    case 'partially_dispensed':
                                        $statusClass = 'badge-status-partial';
                                        $statusText = 'Partial';
                                        break;
                                    case 'dispensed':
                                        $statusClass = 'badge-status-dispensed';
                                        $statusText = 'Dispensed';
                                        break;
                                    case 'cancelled':
                                        $statusClass = 'badge-status-cancelled';
                                        $statusText = 'Cancelled';
                                        break;
                                }
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td>
                                <?php if ($prescription['status'] !== 'dispensed' && $prescription['status'] !== 'cancelled' && $prescription['remaining_quantity'] > 0): ?>
                                    <button 
                                        type="button" 
                                        class="btn-dispense" 
                                        onclick="openDispenseModal(<?= $prescription['id'] ?>, '<?= esc($prescription['medicine_name'], 'js') ?>', <?= $prescription['remaining_quantity'] ?>, <?= $prescription['medicine_stock'] ?>, '<?= esc($prescription['medicine_unit'] ?? 'unit', 'js') ?>')"
                                        <?= $prescription['medicine_stock'] < 1 ? 'disabled' : '' ?>>
                                        Dispense
                                    </button>
                                <?php else: ?>
                                    <span style="color: #9ca3af; font-size: 12px;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Dispense Modal -->
<div class="modal-overlay" id="dispenseModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Dispense Medicine</div>
            <button type="button" class="modal-close" onclick="closeDispenseModal()">&times;</button>
        </div>
        
        <form action="<?= base_url('pharmacy/dashboard/dispense') ?>" method="post" id="dispenseForm">
            <?= csrf_field() ?>
            <input type="hidden" id="prescription_id" name="prescription_id">
            
            <div class="form-group">
                <label class="form-label">Medicine</label>
                <div style="padding: 10px 12px; background: #f9fafb; border-radius: 8px; font-weight: 600;" id="modal_medicine_name">—</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Remaining Quantity to Dispense</label>
                <div style="padding: 10px 12px; background: #f9fafb; border-radius: 8px; font-weight: 600;" id="modal_remaining_qty">—</div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Available Stock</label>
                <div style="padding: 10px 12px; background: #f9fafb; border-radius: 8px; font-weight: 600;" id="modal_available_stock">—</div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="dispense_quantity">Quantity to Dispense *</label>
                <input type="number" 
                       class="form-input" 
                       id="dispense_quantity" 
                       name="dispense_quantity" 
                       min="1"
                       step="1"
                       required>
                <div class="info-text" id="max_quantity_info">Enter the quantity to dispense</div>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeDispenseModal()">Cancel</button>
                <button type="submit" class="btn-primary">Confirm Dispense</button>
            </div>
        </form>
    </div>
</div>

<script>
let maxDispenseQuantity = 0;
let availableStock = 0;

function openDispenseModal(prescriptionId, medicineName, remainingQty, stock, unit) {
    document.getElementById('prescription_id').value = prescriptionId;
    document.getElementById('modal_medicine_name').textContent = medicineName;
    document.getElementById('modal_remaining_qty').textContent = remainingQty + ' ' + unit;
    document.getElementById('modal_available_stock').textContent = stock + ' ' + unit;
    
    maxDispenseQuantity = Math.min(remainingQty, stock);
    availableStock = stock;
    
    const quantityInput = document.getElementById('dispense_quantity');
    quantityInput.max = maxDispenseQuantity;
    quantityInput.value = '';
    
    if (stock < remainingQty) {
        document.getElementById('max_quantity_info').innerHTML = 
            '<span style="color: #dc2626;">⚠️ Insufficient stock. Maximum: ' + maxDispenseQuantity + ' ' + unit + '</span>';
    } else {
        document.getElementById('max_quantity_info').textContent = 
            'Maximum: ' + maxDispenseQuantity + ' ' + unit;
    }
    
    document.getElementById('dispenseModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeDispenseModal() {
    document.getElementById('dispenseModal').classList.remove('active');
    document.body.style.overflow = '';
    document.getElementById('dispenseForm').reset();
}

// Close modal when clicking outside
document.getElementById('dispenseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDispenseModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDispenseModal();
    }
});

// Validate quantity on input
document.getElementById('dispense_quantity').addEventListener('input', function(e) {
    const value = parseInt(this.value);
    if (value > maxDispenseQuantity) {
        this.setCustomValidity('Quantity cannot exceed ' + maxDispenseQuantity);
    } else if (value > availableStock) {
        this.setCustomValidity('Insufficient stock. Available: ' + availableStock);
    } else {
        this.setCustomValidity('');
    }
});

// Inventory search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('inventorySearchInput');
    const categoryFilter = document.getElementById('inventoryCategoryFilter');
    const stockFilter = document.getElementById('inventoryStockFilter');
    const tableBody = document.getElementById('inventoryTableBody');

    if (!tableBody) return;

    function filterInventory() {
        const searchTerm = (searchInput?.value || '').toLowerCase();
        const categoryValue = categoryFilter?.value || '';
        const stockValue = stockFilter?.value || '';
        const rows = tableBody.querySelectorAll('tr');

        rows.forEach(row => {
            const searchData = row.getAttribute('data-search') || '';
            const category = row.getAttribute('data-category') || '';
            const stockStatus = row.getAttribute('data-stock-status') || '';

            const matchesSearch = searchData.includes(searchTerm);
            const matchesCategory = !categoryValue || category === categoryValue;
            const matchesStock = !stockValue || stockStatus === stockValue;

            if (matchesSearch && matchesCategory && matchesStock) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterInventory);
    }
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterInventory);
    }
    if (stockFilter) {
        stockFilter.addEventListener('change', filterInventory);
    }
});
</script>

<?= $this->endSection() ?>
