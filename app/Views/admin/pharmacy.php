<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Pharmacy Inventory</div>
        <div class="page-subtitle">Manage and monitor all medicines in the hospital inventory.</div>
    </div>
</div>

<style>
    .pharmacy-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
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
    .pharmacy-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
    }
    .pharmacy-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .pharmacy-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }
    .search-filter {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }
    .search-input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
    }
    .filter-select {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        background: #ffffff;
    }
    .medicines-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .medicines-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .medicines-table th {
        text-align: left;
        padding: 12px;
        font-weight: 600;
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
    }
    .medicines-table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
    }
    .medicines-table tbody tr:hover {
        background: #f9fafb;
    }
    .medicine-name {
        font-weight: 600;
        color: #111827;
    }
    .category-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
        background: #e0e7ff;
        color: #4338ca;
    }
    .stock-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .stock-badge.out-of-stock {
        background: #fee2e2;
        color: #991b1b;
    }
    .stock-badge.low-stock {
        background: #fef3c7;
        color: #92400e;
    }
    .stock-badge.in-stock {
        background: #dcfce7;
        color: #166534;
    }
    .price {
        font-weight: 600;
        color: #111827;
    }
    .total-value {
        font-weight: 600;
        color: #16a34a;
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
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
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
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e5e7eb;
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
        transition: background 0.2s ease;
    }
    .modal-close:hover {
        background: #f3f4f6;
    }
    .form-group {
        margin-bottom: 16px;
    }
    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #111827;
    }
    .form-select,
    .form-input {
        width: 100%;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 13px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .form-select:focus,
    .form-input:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    .form-input[type="number"] {
        -moz-appearance: textfield;
    }
    .form-input[type="number"]::-webkit-outer-spin-button,
    .form-input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }
    .btn-primary {
        padding: 8px 16px;
        border-radius: 999px;
        border: none;
        background: #16a34a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .btn-primary:hover {
        background: #15803d;
    }
    .btn-secondary {
        padding: 8px 16px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #111827;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .btn-secondary:hover {
        background: #f9fafb;
    }
    .btn-restock {
        padding: 8px 16px;
        border-radius: 999px;
        border: none;
        background: #16a34a;
        color: #ffffff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .btn-restock:hover {
        background: #15803d;
    }
    .current-stock {
        font-size: 12px;
        color: #6b7280;
        margin-top: 6px;
    }
</style>

<?php if (session()->getFlashdata('error')): ?>
    <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fecaca;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php 
$successFlash = session()->getFlashdata('success');
if ($successFlash): ?>
    <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #bbf7d0;">
        <?= esc($successFlash) ?>
    </div>
<?php endif; ?>

<div class="pharmacy-stats">
    <div class="stat-card">
        <div class="stat-label">Total Medicines</div>
        <div class="stat-value"><?= number_format($totalMedicines ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Out of Stock</div>
        <div class="stat-value danger"><?= number_format($outOfStockCount ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Low Stock (< 10)</div>
        <div class="stat-value warning"><?= number_format($lowStockCount ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Inventory Value</div>
        <div class="stat-value success">₱<?= number_format($totalInventoryValue ?? 0, 2) ?></div>
    </div>
</div>

<div class="pharmacy-card">
    <div class="pharmacy-header">
        <div class="pharmacy-title">Medicine Inventory</div>
        <button type="button" class="btn-restock" onclick="openRestockModal()">
            📦 Order/Restock Medicine
        </button>
    </div>

    <div class="search-filter">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by medicine name or category...">
        <select class="filter-select" id="categoryFilter">
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
        <select class="filter-select" id="stockFilter">
            <option value="">All Stock Status</option>
            <option value="out">Out of Stock</option>
            <option value="low">Low Stock</option>
            <option value="in">In Stock</option>
        </select>
    </div>

    <?php if (empty($medicines)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">💊</div>
            <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">No Medicines Found</div>
            <div>No medicines have been added to the inventory yet.</div>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="medicines-table">
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
                <tbody id="medicinesTableBody">
                    <?php foreach ($medicines as $medicine): ?>
                        <tr data-search="<?= strtolower(esc($medicine['medicine_name'] . ' ' . ($medicine['category'] ?? '') . ' ' . ($medicine['unit'] ?? ''))) ?>" 
                            data-category="<?= esc($medicine['category'] ?? '') ?>"
                            data-stock-status="<?= $medicine['is_out_of_stock'] ? 'out' : ($medicine['is_low_stock'] ? 'low' : 'in') ?>">
                            <td>
                                <div class="medicine-name"><?= esc($medicine['medicine_name']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($medicine['category'])): ?>
                                    <span class="category-badge"><?= esc($medicine['category']) ?></span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $stockClass = 'out-of-stock';
                                $stockText = 'Out of Stock';
                                if ($medicine['stock_quantity'] > 0) {
                                    if ($medicine['stock_quantity'] < 10) {
                                        $stockClass = 'low-stock';
                                        $stockText = 'Low Stock';
                                    } else {
                                        $stockClass = 'in-stock';
                                        $stockText = 'In Stock';
                                    }
                                }
                                ?>
                                <span class="stock-badge <?= $stockClass ?>">
                                    <?= number_format($medicine['stock_quantity']) ?>
                                </span>
                            </td>
                            <td><?= esc($medicine['unit'] ?? 'unit') ?></td>
                            <td>
                                <span class="price">₱<?= number_format($medicine['unit_price'], 2) ?></span>
                            </td>
                            <td>
                                <span class="total-value">₱<?= number_format($medicine['total_value'], 2) ?></span>
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

<!-- Restock Modal -->
<div class="modal-overlay" id="restockModal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Order/Restock Medicine</div>
            <button type="button" class="modal-close" onclick="closeRestockModal()">&times;</button>
        </div>
        
        
        <form action="/WebSys_HMS_G3/admin/pharmacy/restock" method="post" id="restockForm">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label class="form-label" for="medicine_id">Select Medicine *</label>
                <select class="form-select" id="medicine_id" name="medicine_id" required onchange="updateCurrentStock()">
                    <option value="">-- Select Medicine --</option>
                    <?php foreach ($medicines as $medicine): ?>
                        <option value="<?= $medicine['id'] ?>" 
                                data-stock="<?= $medicine['stock_quantity'] ?>"
                                data-unit="<?= esc($medicine['unit'] ?? 'unit') ?>"
                                data-price="<?= $medicine['unit_price'] ?>">
                            <?= esc($medicine['medicine_name']) ?> 
                            (<?= esc($medicine['category'] ?? 'N/A') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="current-stock" id="currentStockInfo" style="display: none;">
                    Current Stock: <strong id="currentStockValue">0</strong> <span id="currentStockUnit"></span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="quantity">Quantity to Add *</label>
                <input type="number" 
                       class="form-input" 
                       id="quantity" 
                       name="quantity" 
                       min="1" 
                       step="1"
                       placeholder="Enter quantity to add to stock"
                       required>
                <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                    Enter the number of units to add to the current stock
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="supplier">Supplier (Optional)</label>
                <input type="text" 
                       class="form-input" 
                       id="supplier" 
                       name="supplier" 
                       placeholder="Enter supplier name">
            </div>
            
            <div class="form-group">
                <label class="form-label" for="notes">Notes (Optional)</label>
                <textarea class="form-input" 
                          id="notes" 
                          name="notes" 
                          rows="3"
                          placeholder="Additional notes about this order/restock"></textarea>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeRestockModal()">Cancel</button>
                <button type="submit" class="btn-primary">Confirm Order/Restock</button>
            </div>
        </form>
    </div>
</div>

<script>
function openRestockModal() {
    document.getElementById('restockModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeRestockModal() {
    document.getElementById('restockModal').classList.remove('active');
    document.body.style.overflow = '';
    document.getElementById('restockForm').reset();
    document.getElementById('currentStockInfo').style.display = 'none';
}

function updateCurrentStock() {
    const select = document.getElementById('medicine_id');
    const selectedOption = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('currentStockInfo');
    const stockValue = document.getElementById('currentStockValue');
    const stockUnit = document.getElementById('currentStockUnit');
    
    if (select.value) {
        const currentStock = selectedOption.getAttribute('data-stock');
        const unit = selectedOption.getAttribute('data-unit');
        stockValue.textContent = currentStock;
        stockUnit.textContent = unit;
        stockInfo.style.display = 'block';
    } else {
        stockInfo.style.display = 'none';
    }
}

// Close modal when clicking outside
document.getElementById('restockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRestockModal();
    }
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRestockModal();
    }
});

// Handle restock form submission
document.getElementById('restockForm').addEventListener('submit', function(e) {
    const medicineId = document.getElementById('medicine_id').value;
    const quantity = document.getElementById('quantity').value;
    console.log('Form submitting - Medicine ID:', medicineId, 'Quantity:', quantity);
    console.log('Form action:', this.action);
    console.log('Form method:', this.method);
    
    if (!medicineId || !quantity || quantity <= 0) {
        e.preventDefault();
        alert('Please select a medicine and enter a valid quantity.');
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';
    }
    
    // Let the form submit normally
    return true;
});

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockFilter = document.getElementById('stockFilter');
    const tableBody = document.getElementById('medicinesTableBody');

    function filterMedicines() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryValue = categoryFilter.value;
        const stockValue = stockFilter.value;
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
        searchInput.addEventListener('input', filterMedicines);
    }
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterMedicines);
    }
    if (stockFilter) {
        stockFilter.addEventListener('change', filterMedicines);
    }
});
</script>

<?= $this->endSection() ?>

