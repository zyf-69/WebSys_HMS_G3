<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Assigned Patients</div>
        <div class="page-subtitle">View and manage all inpatients assigned to your ward.</div>
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

<div class="records-card">
    <table class="records-table">
        <thead>
            <tr>
                <th>Patient Name</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Room/Bed</th>
                <th>Admission Date</th>
                <th>Attending Doctor</th>
                <th>Vitals</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-icon">👤</div>
                        <div>No assigned patients found</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><strong><?= esc($patient['full_name'] ?? 'N/A') ?></strong></td>
                        <td><?= esc($patient['age'] ?? 'N/A') ?></td>
                        <td><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></td>
                        <td>
                            <?php if (!empty($patient['room_number']) || !empty($patient['bed_number'])): ?>
                                <span class="badge badge-room">
                                    <?= esc($patient['room_number'] ?? 'N/A') ?> / <?= esc($patient['bed_number'] ?? 'N/A') ?>
                                </span>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= esc($patient['admission_datetime'] ? date('M d, Y', strtotime($patient['admission_datetime'])) : 'N/A') ?></td>
                        <td><?= esc($patient['doctor_name'] ?? 'N/A') ?><?= !empty($patient['specialization']) ? ' (' . esc($patient['specialization']) . ')' : '' ?></td>
                        <td>
                            <?php if (!empty($patient['blood_pressure']) || !empty($patient['heart_rate']) || !empty($patient['temperature'])): ?>
                                BP: <?= esc($patient['blood_pressure'] ?? 'N/A') ?> | 
                                HR: <?= esc($patient['heart_rate'] ?? 'N/A') ?> | 
                                Temp: <?= esc($patient['temperature'] ?? 'N/A') ?>°C
                            <?php else: ?>
                                <span style="color: #9ca3af;">Not recorded</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

