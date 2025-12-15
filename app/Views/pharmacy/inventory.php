<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Medicine Inventory</div>
        <div class="page-subtitle">Manage and track medicine stock levels.</div>
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
        <div class="stat-label">Total Inventory Value</div>
        <div class="stat-value success">₱<?= number_format($totalInventoryValue ?? 0, 2) ?></div>
    </div>
</div>

<div class="section-card">
    <div class="section-header">
        <div class="section-title">📦 Medicine Inventory</div>
    </div>

    <div class="search-filter">
        <input type="text" class="search-input" id="inventorySearchInput" placeholder="Search by medicine name or category...">
        <select class="filter-select" id="inventoryCategoryFilter">
            <option value="">All Categories</option>
            <?php foreach ($categories ?? [] as $category): ?>
                <option value="<?= esc($category) ?>"><?= esc($category) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="filter-select" id="inventoryStockFilter">
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
            <table class="data-table">
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
                <tbody>
                    <?php foreach ($medicines as $medicine): ?>
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #111827;"><?= esc($medicine['medicine_name']) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($medicine['category'])): ?>
                                    <span class="badge badge-category"><?= esc($medicine['category']) ?></span>
                                <?php else: ?>
                                    <span style="color: #6b7280;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-weight: 600;"><?= number_format($medicine['stock_quantity']) ?></span>
                            </td>
                            <td>
                                <span style="color: #6b7280;"><?= esc($medicine['unit'] ?? 'unit') ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600;">₱<?= number_format($medicine['unit_price'], 2) ?></span>
                            </td>
                            <td>
                                <span style="font-weight: 600; color: #16a34a;">₱<?= number_format($medicine['total_value'], 2) ?></span>
                            </td>
                            <td>
                                <?php
                                if ($medicine['is_out_of_stock']) {
                                    $stockBadge = 'badge-stock-out';
                                    $stockText = 'Out of Stock';
                                } elseif ($medicine['is_low_stock']) {
                                    $stockBadge = 'badge-stock-low';
                                    $stockText = 'Low Stock';
                                } else {
                                    $stockBadge = 'badge-stock-ok';
                                    $stockText = 'In Stock';
                                }
                                ?>
                                <span class="badge <?= $stockBadge ?>"><?= $stockText ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
// Search and filter functionality
document.getElementById('inventorySearchInput').addEventListener('input', filterInventory);
document.getElementById('inventoryCategoryFilter').addEventListener('change', filterInventory);
document.getElementById('inventoryStockFilter').addEventListener('change', filterInventory);

function filterInventory() {
    const searchTerm = document.getElementById('inventorySearchInput').value.toLowerCase();
    const categoryFilter = document.getElementById('inventoryCategoryFilter').value;
    const stockFilter = document.getElementById('inventoryStockFilter').value;
    const rows = document.querySelectorAll('.data-table tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const category = row.querySelector('.badge-category')?.textContent.trim() || '';
        const stockStatus = row.querySelector('.badge')?.textContent.toLowerCase().trim() || '';
        
        const searchMatch = !searchTerm || text.includes(searchTerm);
        const categoryMatch = !categoryFilter || category === categoryFilter;
        
        let stockMatch = true;
        if (stockFilter === 'out') {
            stockMatch = stockStatus === 'out of stock';
        } else if (stockFilter === 'low') {
            stockMatch = stockStatus === 'low stock';
        } else if (stockFilter === 'in') {
            stockMatch = stockStatus === 'in stock';
        }
        
        row.style.display = (searchMatch && categoryMatch && stockMatch) ? '' : 'none';
    });
}
</script>

<?= $this->endSection() ?>

