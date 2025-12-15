<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Medical Reports</div>
        <div class="page-subtitle">Generate medical reports for your patients based on their medical history.</div>
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
    .btn-link {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
        font-size: 13px;
        background: #eff6ff;
        padding: 6px 12px;
        border-radius: 6px;
        display: inline-block;
    }
    .btn-link:hover {
        background: #dbeafe;
        text-decoration: none;
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
                <th>Patient Code</th>
                <th>Age</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="5" class="empty-state">
                        <div class="empty-state-icon">👤</div>
                        <div>No patients found</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><strong><?= esc($patient['full_name'] ?? 'N/A') ?></strong></td>
                        <td><?= esc($patient['patient_code'] ?? 'N/A') ?></td>
                        <td><?= esc($patient['age'] ?? 'N/A') ?></td>
                        <td><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></td>
                        <td>
                            <a href="/WebSys_HMS_G3/doctor/medical-reports/generate/<?= esc($patient['id']) ?>" class="btn-link" target="_blank">Generate Report</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>

