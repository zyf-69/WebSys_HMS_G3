<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Test Requests</div>
        <div class="page-subtitle">Manage and update lab test request status.</div>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="card" style="background: #fee2e2; border-color: #fecaca; color: #991b1b; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px;">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="card" style="background: #dcfce7; border-color: #bbf7d0; color: #166534; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px;">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="grid grid-4" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Tests</div>
        </div>
        <div class="card-value"><?= number_format($totalTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Pending</div>
        </div>
        <div class="card-value" style="color: #dc2626;"><?= number_format($pendingTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">In Progress</div>
        </div>
        <div class="card-value" style="color: #2563eb;"><?= number_format($inProgressTests ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Completed</div>
        </div>
        <div class="card-value" style="color: #16a34a;"><?= number_format($completedTests ?? 0) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">All Test Requests</div>
    </div>
    <?php if (!empty($tests)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Test Type</th>
                    <th>Test Name</th>
                    <th>Doctor</th>
                    <th>Status</th>
                    <th>Request Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tests as $test): ?>
                    <tr>
                        <td>#<?= esc($test['id']) ?></td>
                        <td style="font-weight: 500;"><?= esc($test['patient_name'] ?? 'N/A') ?></td>
                        <td><?= esc($test['test_type'] ?? 'N/A') ?></td>
                        <td><?= esc($test['test_name'] ?? 'N/A') ?></td>
                        <td><?= esc($test['doctor_name'] ?? 'N/A') ?></td>
                        <td>
                            <span style="display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 500;
                                <?php
                                $status = $test['status'] ?? 'pending';
                                if ($status === 'completed') {
                                    echo 'background: #d1fae5; color: #065f46;';
                                } elseif ($status === 'in_progress') {
                                    echo 'background: #dbeafe; color: #1e40af;';
                                } elseif ($status === 'cancelled') {
                                    echo 'background: #fee2e2; color: #991b1b;';
                                } else {
                                    echo 'background: #fef3c7; color: #92400e;';
                                }
                                ?>
                            ">
                                <?= ucfirst(str_replace('_', ' ', $status)) ?>
                            </span>
                        </td>
                        <td><?= $test['created_at'] ? date('M d, Y', strtotime($test['created_at'])) : 'N/A' ?></td>
                        <td>
                            <form action="/WebSys_HMS_G3/lab/test-requests/update-status/<?= esc($test['id']) ?>" method="post" style="display: inline-block;">
                                <?= csrf_field() ?>
                                <select name="status" onchange="this.form.submit()" style="padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 12px; cursor: pointer;">
                                    <option value="pending" <?= ($test['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="in_progress" <?= ($test['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="completed" <?= ($test['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= ($test['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
            <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;">⚗</div>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">No test requests found</div>
            <div style="font-size: 13px;">Test requests will appear here when doctors create lab test requests.</div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

