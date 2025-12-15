<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Prescription Management</div>
        <div class="page-subtitle">View and track all patient prescriptions.</div>
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        text-align: center;
    }
    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        margin: 8px 0;
    }
    .stat-label {
        font-size: 13px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .records-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        overflow-x: auto;
    }
    .records-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .records-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .records-table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .records-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
    }
    .records-table tbody tr:hover {
        background: #f9fafb;
    }
    .badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-partially-dispensed {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-dispensed {
        background: #dcfce7;
        color: #166534;
    }
    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Prescriptions</div>
        <div class="stat-value"><?= esc($totalPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value"><?= esc($pendingPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dispensed</div>
        <div class="stat-value"><?= esc($dispensedPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partially Dispensed</div>
        <div class="stat-value"><?= esc($partiallyDispensed ?? 0) ?></div>
    </div>
</div>

<div class="records-card">
    <table class="records-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Medicine</th>
                <th>Prescribed Qty</th>
                <th>Dispensed Qty</th>
                <th>Prescription Date</th>
                <th>Status</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($prescriptions)): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #6b7280;">
                        No prescriptions found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td><strong><?= esc($prescription['patient_name'] ?? 'N/A') ?></strong></td>
                        <td><?= esc($prescription['doctor_name'] ?? 'N/A') ?></td>
                        <td><?= esc($prescription['medicine_name'] ?? 'N/A') ?></td>
                        <td><?= esc($prescription['prescribed_quantity'] ?? 0) ?> <?= esc($prescription['unit'] ?? '') ?></td>
                        <td><?= esc($prescription['dispensed_quantity'] ?? 0) ?> <?= esc($prescription['unit'] ?? '') ?></td>
                        <td><?= esc($prescription['prescription_date'] ? date('M d, Y', strtotime($prescription['prescription_date'])) : 'N/A') ?></td>
                        <td>
                            <?php
                            $status = $prescription['status'] ?? 'pending';
                            $badgeClass = 'badge-' . str_replace('_', '-', $status);
                            ?>
                            <span class="badge <?= esc($badgeClass) ?>"><?= esc(ucfirst(str_replace('_', ' ', $status))) ?></span>
                        </td>
                        <td>
                            <?php
                            $stockQty = $prescription['stock_quantity'] ?? 0;
                            $prescribedQty = $prescription['prescribed_quantity'] ?? 0;
                            if ($stockQty < $prescribedQty):
                            ?>
                                <span style="color: #ef4444;">Low (<?= esc($stockQty) ?>)</span>
                            <?php else: ?>
                                <span style="color: #059669;">Available (<?= esc($stockQty) ?>)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

