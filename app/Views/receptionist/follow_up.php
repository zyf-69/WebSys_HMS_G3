<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Follow-up Management</div>
        <div class="page-subtitle">View follow-up appointments scheduled by doctors.</div>
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
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 24px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-label {
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 6px;
        color: #374151;
    }
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
    }
    .form-textarea {
        min-height: 80px;
        resize: vertical;
    }
    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }
    .btn-primary {
        background: #3b82f6;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-primary:hover {
        background: #2563eb;
    }
    .btn-secondary {
        background: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: #f9fafb;
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
    .badge-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-completed {
        background: #dcfce7;
        color: #166534;
    }
    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }
    .badge-no-show {
        background: #fef3c7;
        color: #92400e;
    }
    .btn-status {
        padding: 4px 10px;
        border: none;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        margin: 2px;
    }
    .btn-status.completed {
        background: #10b981;
        color: #ffffff;
    }
    .btn-status.cancelled {
        background: #ef4444;
        color: #ffffff;
    }
    .btn-status.no-show {
        background: #f59e0b;
        color: #ffffff;
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Follow-ups</div>
        <div class="stat-value"><?= esc($totalFollowUps ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Scheduled</div>
        <div class="stat-value"><?= esc($scheduledFollowUps ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed</div>
        <div class="stat-value"><?= esc($completedFollowUps ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Upcoming Today</div>
        <div class="stat-value"><?= esc($upcomingToday ?? 0) ?></div>
    </div>
</div>

<div class="records-card">
    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Follow-up List</h3>
    <table class="records-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Follow-up Date</th>
                <th>Follow-up Time</th>
                <th>Reason</th>
                <th>Status</th>
                <th>Original Appointment</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($followUps)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #6b7280;">
                        No follow-up appointments found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($followUps as $followUp): ?>
                    <tr>
                        <td><strong><?= esc($followUp['patient_name'] ?? 'N/A') ?></strong></td>
                        <td><?= esc($followUp['doctor_name'] ?? 'N/A') ?></td>
                        <td><?= esc($followUp['follow_up_date'] ? date('M d, Y', strtotime($followUp['follow_up_date'])) : 'N/A') ?></td>
                        <td><?= esc($followUp['follow_up_time'] ? date('H:i', strtotime($followUp['follow_up_time'])) : 'N/A') ?></td>
                        <td><?= esc($followUp['reason'] ?? 'N/A') ?></td>
                        <td>
                            <?php
                            $status = $followUp['status'] ?? 'scheduled';
                            $badgeClass = 'badge-' . $status;
                            ?>
                            <span class="badge <?= esc($badgeClass) ?>"><?= esc(ucfirst($status)) ?></span>
                        </td>
                        <td>
                            <?php if (!empty($followUp['original_appointment_date'])): ?>
                                <?= esc(date('M d, Y', strtotime($followUp['original_appointment_date']))) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

