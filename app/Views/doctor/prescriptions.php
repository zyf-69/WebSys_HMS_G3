<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Prescriptions</div>
        <div class="page-subtitle">Manage prescriptions for your patients.</div>
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
        margin-bottom: 24px;
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
    .badge-partially-dispensed {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-dispensed {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        color: #111827;
        margin-bottom: 6px;
    }
    .form-label .required {
        color: #dc2626;
    }
    .form-input, .form-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        color: #111827;
    }
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        color: #111827;
        min-height: 80px;
        resize: vertical;
    }
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
    }
    .btn-primary {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #059669;
    }
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
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
        <div class="stat-label">Total Prescriptions</div>
        <div class="stat-value"><?= esc($totalPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value"><?= esc($pendingPrescriptions ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Partially Dispensed</div>
        <div class="stat-value"><?= esc($partiallyDispensed ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Dispensed</div>
        <div class="stat-value"><?= esc($dispensedPrescriptions ?? 0) ?></div>
    </div>
</div>

<div class="form-card" style="margin-bottom: 24px;">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 600; color: #111827;">Create New Prescription</h3>
    <form action="/WebSys_HMS_G3/doctor/prescriptions" method="post">
        <?= csrf_field() ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label class="form-label">Patient <span class="required">*</span></label>
                <select name="patient_id" class="form-select" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients ?? [] as $patient): ?>
                        <option value="<?= esc($patient['id']) ?>">
                            <?= esc(trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Medicine <span class="required">*</span></label>
                <select name="medicine_id" class="form-select" required>
                    <option value="">Select Medicine</option>
                    <?php foreach ($medicines ?? [] as $medicine): ?>
                        <option value="<?= esc($medicine['id']) ?>" data-stock="<?= esc($medicine['stock_quantity']) ?>">
                            <?= esc($medicine['medicine_name']) ?> (Stock: <?= esc($medicine['stock_quantity']) ?> <?= esc($medicine['unit']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Quantity <span class="required">*</span></label>
                <input type="number" name="prescribed_quantity" class="form-input" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">Prescription Date</label>
                <input type="date" name="prescription_date" class="form-input" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-textarea" placeholder="Additional notes or instructions..."></textarea>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="this.form.reset()">Clear</button>
            <button type="submit" class="btn-primary">Create Prescription</button>
        </div>
    </form>
</div>

<div class="records-card">
    <table class="records-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Prescription Date</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($prescriptions)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon">💊</div>
                        <div>No prescriptions found</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td>
                            <?php
                            $patientName = trim(($prescription['first_name'] ?? '') . ' ' . ($prescription['middle_name'] ?? '') . ' ' . ($prescription['last_name'] ?? ''));
                            echo esc($patientName ?: 'N/A');
                            ?>
                        </td>
                        <td><strong><?= esc($prescription['medicine_name'] ?? 'N/A') ?></strong></td>
                        <td>
                            <?= esc($prescription['prescribed_quantity'] ?? 0) ?> <?= esc($prescription['unit'] ?? '') ?>
                            <?php if ($prescription['dispensed_quantity'] > 0): ?>
                                <br><small style="color: #6b7280;">Dispensed: <?= esc($prescription['dispensed_quantity']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $status = $prescription['status'] ?? 'pending';
                            $statusClass = 'badge-' . str_replace('_', '-', $status);
                            ?>
                            <span class="badge <?= esc($statusClass) ?>"><?= esc(ucfirst(str_replace('_', ' ', $status))) ?></span>
                        </td>
                        <td><?= esc($prescription['prescription_date'] ? date('M d, Y', strtotime($prescription['prescription_date'])) : 'N/A') ?></td>
                        <td><?= esc($prescription['notes'] ? substr($prescription['notes'], 0, 50) . (strlen($prescription['notes']) > 50 ? '...' : '') : '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

