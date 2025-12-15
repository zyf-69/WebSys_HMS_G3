<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Prescriptions</div>
        <div class="page-subtitle">Manage and fulfill prescriptions.</div>
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
        <div class="stat-label">Total Prescriptions</div>
        <div class="stat-value"><?= number_format($totalPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value warning"><?= number_format($pendingPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partially Dispensed</div>
        <div class="stat-value"><?= number_format($partiallyDispensed ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dispensed</div>
        <div class="stat-value success"><?= number_format($dispensedPrescriptions ?? 0) ?></div>
    </div>
</div>

<div class="section-card">
    <div class="section-header">
        <div class="section-title">💊 Prescriptions</div>
    </div>

    <div class="search-filter">
        <input type="text" class="search-input" id="searchInput" placeholder="Search by patient name, doctor, or medicine...">
        <select class="filter-select" id="statusFilter">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="partially_dispensed">Partially Dispensed</option>
            <option value="dispensed">Dispensed</option>
            <option value="cancelled">Cancelled</option>
        </select>
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
                                    <button class="btn-dispense" onclick="openDispenseModal(<?= $prescription['id'] ?>, '<?= esc($prescription['medicine_name'], 'js') ?>', <?= $prescription['remaining_quantity'] ?>, <?= $prescription['medicine_stock'] ?>)">
                                        Dispense
                                    </button>
                                <?php else: ?>
                                    <span style="color: #6b7280; font-size: 12px;">-</span>
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
            <button class="modal-close" onclick="closeDispenseModal()">&times;</button>
        </div>
        <form method="POST" action="<?= base_url('pharmacy/prescriptions/dispense') ?>">
            <input type="hidden" name="prescription_id" id="modalPrescriptionId">
            <div class="form-group">
                <label class="form-label">Medicine Name</label>
                <input type="text" class="form-input" id="modalMedicineName" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Remaining Quantity</label>
                <input type="text" class="form-input" id="modalRemainingQty" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Available Stock</label>
                <input type="text" class="form-input" id="modalAvailableStock" readonly>
            </div>
            <div class="form-group">
                <label class="form-label">Quantity to Dispense <span style="color: #dc2626;">*</span></label>
                <input type="number" class="form-input" name="dispense_quantity" id="modalDispenseQty" min="1" required>
                <div class="info-text">Enter the quantity you want to dispense</div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="closeDispenseModal()">Cancel</button>
                <button type="submit" class="btn-primary">Dispense</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDispenseModal(prescriptionId, medicineName, remainingQty, availableStock) {
    document.getElementById('modalPrescriptionId').value = prescriptionId;
    document.getElementById('modalMedicineName').value = medicineName;
    document.getElementById('modalRemainingQty').value = remainingQty;
    document.getElementById('modalAvailableStock').value = availableStock;
    document.getElementById('modalDispenseQty').max = Math.min(remainingQty, availableStock);
    document.getElementById('modalDispenseQty').value = Math.min(remainingQty, availableStock);
    document.getElementById('dispenseModal').classList.add('active');
}

function closeDispenseModal() {
    document.getElementById('dispenseModal').classList.remove('active');
}

// Close modal on overlay click
document.getElementById('dispenseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDispenseModal();
    }
});

// Search and filter functionality
document.getElementById('searchInput').addEventListener('input', filterTable);
document.getElementById('statusFilter').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.data-table tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        const statusMatch = !statusFilter || row.querySelector('.badge')?.textContent.toLowerCase().includes(statusFilter.replace('_', ' '));
        const searchMatch = !searchTerm || text.includes(searchTerm);
        
        row.style.display = (statusMatch && searchMatch) ? '' : 'none';
    });
}
</script>

<?= $this->endSection() ?>

