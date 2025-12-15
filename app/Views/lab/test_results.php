<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Test Results</div>
        <div class="page-subtitle">View completed lab test results.</div>
    </div>
</div>

<div class="grid grid-3" style="margin-bottom: 24px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Completed</div>
        </div>
        <div class="card-value" style="color: #16a34a;"><?= number_format($totalCompleted ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Completed Today</div>
        </div>
        <div class="card-value" style="color: #2563eb;"><?= number_format($todayCompleted ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Test Types</div>
        </div>
        <div class="card-value"><?= number_format(count($byTestType ?? [])) ?></div>
    </div>
</div>

<!-- Test Types Summary -->
<?php if (!empty($byTestType)): ?>
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Completed Tests by Type</div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Test Type</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($byTestType as $type => $count): ?>
                <tr>
                    <td><?= esc($type) ?></td>
                    <td style="font-weight: 600;"><?= number_format($count) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size: 14px; font-weight: 600;">Completed Test Results</div>
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
                    <th>Completed Date</th>
                    <th>Notes</th>
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
                        <td><?= $test['updated_at'] ? date('M d, Y h:i A', strtotime($test['updated_at'])) : 'N/A' ?></td>
                        <td><?= esc($test['notes'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #6b7280;">
            <div style="font-size: 64px; margin-bottom: 16px; opacity: 0.3;">📊</div>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px;">No completed test results found</div>
            <div style="font-size: 13px;">Completed test results will appear here once tests are marked as completed.</div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

