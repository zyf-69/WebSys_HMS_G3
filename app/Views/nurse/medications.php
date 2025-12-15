<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Medications</div>
        <div class="page-subtitle">View pending medications for assigned patients.</div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="padding: 12px; background: #fee2e2; color: #991b1b; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fecaca;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div style="padding: 12px; background: #dcfce7; color: #166534; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #bbf7d0;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

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
    .records-table tbody tr:last-child td {
        border-bottom: none;
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
    .badge-low-stock {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-room {
        background: #dbeafe;
        color: #1e40af;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 16px;
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Pending</div>
        <div class="stat-value"><?= esc($totalPending ?? 0) ?></div>
    </div>
</div>

<div class="records-card">
    <table class="records-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Room/Bed</th>
                <th>Medicine</th>
                <th>Quantity</th>
                        <th>Prescribed By</th>
                        <th>Prescription Date</th>
                        <th>Stock Status</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medications)): ?>
                        <tr>
                            <td colspan="9" class="empty-state">
                                <div class="empty-state-icon">💊</div>
                                <div>No pending medications found</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($medications as $medication): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($medication['patient_name'] ?? 'N/A') ?></strong>
                                    <?php if (!empty($medication['patient_code'])): ?>
                                        <br><small style="color: #6b7280;"><?= esc($medication['patient_code']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($medication['room_number']) || !empty($medication['bed_number'])): ?>
                                        <span class="badge badge-room">
                                            <?= esc($medication['room_number'] ?? 'N/A') ?> / <?= esc($medication['bed_number'] ?? 'N/A') ?>
                                        </span>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= esc($medication['medicine_name'] ?? 'N/A') ?></strong></td>
                                <td>
                                    <?= esc($medication['prescribed_quantity'] ?? 0) ?> <?= esc($medication['unit'] ?? '') ?>
                                    <?php if ($medication['dispensed_quantity'] > 0): ?>
                                        <br><small style="color: #6b7280;">Dispensed: <?= esc($medication['dispensed_quantity']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($medication['doctor_name'] ?? 'N/A') ?></td>
                                <td><?= esc($medication['prescription_date'] ? date('M d, Y', strtotime($medication['prescription_date'])) : 'N/A') ?></td>
                                <td>
                                    <?php
                                    $stockQuantity = $medication['stock_quantity'] ?? 0;
                                    $prescribedQuantity = $medication['prescribed_quantity'] ?? 0;
                                    if ($stockQuantity < $prescribedQuantity):
                                    ?>
                                        <span class="badge badge-low-stock">Low Stock (<?= esc($stockQuantity) ?>)</span>
                                    <?php else: ?>
                                        <span style="color: #059669;">Available (<?= esc($stockQuantity) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $status = $medication['status'] ?? 'pending';
                                    $isDispensed = $status === 'dispensed' || ($medication['dispensed_quantity'] ?? 0) >= ($medication['prescribed_quantity'] ?? 0);
                                    ?>
                                    <?php if ($isDispensed): ?>
                                        <span class="badge" style="background: #dcfce7; color: #166534;">Dispensed</span>
                                    <?php elseif ($status === 'partially_dispensed'): ?>
                                        <span class="badge" style="background: #fef3c7; color: #92400e;">Partial</span>
                                    <?php else: ?>
                                        <span class="badge badge-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="post" action="/WebSys_HMS_G3/nurse/medications/update/<?= esc($medication['id']) ?>" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <?php if ($isDispensed): ?>
                                            <input type="hidden" name="action" value="not_dispensed">
                                            <button type="submit" class="btn-action" style="background: #fee2e2; color: #991b1b; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;" onclick="return confirm('Mark this prescription as NOT dispensed? Stock will be restored.')">
                                                Mark Not Dispensed
                                            </button>
                                        <?php else: ?>
                                            <input type="hidden" name="action" value="dispensed">
                                            <button type="submit" class="btn-action" style="background: #10b981; color: #ffffff; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer;" onclick="return confirm('Mark this prescription as DISPENSED? Stock will be deducted.')">
                                                Mark Dispensed
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

