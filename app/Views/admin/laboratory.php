<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Laboratory</div>
        <div class="page-subtitle">Manage laboratory tests and test results.</div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="card" style="background:#ffebe9;border-color:#fecaca;color:#b91c1c;padding:10px 14px;margin-bottom:14px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="card" style="background:#ecfdf3;border-color:#bbf7d0;color:#166534;padding:10px 14px;margin-bottom:14px;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Tests</div>
        </div>
        <div class="card-value"><?= number_format($totalTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending Tests</div>
        </div>
        <div class="card-value" style="color:#dc2626;"><?= number_format($pendingTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Completed Tests</div>
        </div>
        <div class="card-value" style="color:#16a34a;"><?= number_format($completedTests ?? 0) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size:14px;font-weight:600;">All Lab Tests</div>
    </div>
    <?php if (!empty($tests)): ?>
        <table>
            <thead>
                <tr>
                    <th>Test ID</th>
                    <th>Patient</th>
                    <th>Test Type</th>
                    <th>Test Name</th>
                    <th>Doctor</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tests as $test): ?>
                    <tr>
                        <td>#<?= esc($test['id']) ?></td>
                        <td><?= esc(trim(($test['last_name'] ?? '') . ', ' . ($test['first_name'] ?? '') . ' ' . ($test['middle_name'] ?? ''))) ?></td>
                        <td><?= esc($test['test_type'] ?? 'N/A') ?></td>
                        <td><?= esc($test['test_name'] ?? 'N/A') ?></td>
                        <td><?= esc($test['doctor_name'] ?? 'N/A') ?></td>
                        <td>
                            <span style="display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:500;<?= ($test['status'] ?? 'pending') === 'completed' ? 'background:#d1fae5;color:#065f46;' : (($test['status'] ?? 'pending') === 'in_progress' ? 'background:#dbeafe;color:#1e40af;' : 'background:#fef3c7;color:#92400e;') ?>">
                                <?= ucfirst(str_replace('_', ' ', $test['status'] ?? 'pending')) ?>
                            </span>
                        </td>
                        <td><?= $test['created_at'] ? date('M d, Y', strtotime($test['created_at'])) : 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center;padding:40px 20px;color:#6b7280;">
            <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">⚗</div>
            <div style="font-size:14px;font-weight:500;margin-bottom:4px;">No lab tests found</div>
            <div style="font-size:12px;">Create a test to get started.</div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

