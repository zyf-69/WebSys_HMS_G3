<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Generated Report</div>
        <div class="page-subtitle"><?= esc(ucfirst($reportType ?? 'Report')) ?> Report<?= $startDate || $endDate ? ' (' . ($startDate ?? 'All') . ' - ' . ($endDate ?? 'All') . ')' : '' ?></div>
    </div>
    <div>
        <button onclick="window.print()" style="padding:8px 16px;border-radius:999px;border:none;background:#1d4ed8;color:#ffffff;font-size:13px;font-weight:600;cursor:pointer;">Print Report</button>
        <a href="/WebSys_HMS_G3/admin/reports" style="display:inline-block;padding:8px 16px;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;text-decoration:none;color:#111827;font-size:13px;margin-left:8px;">Back</a>
    </div>
</div>

<div class="card">
    <div style="text-align:center;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #e5e7eb;">
        <div style="font-size:20px;font-weight:700;margin-bottom:4px;"><?= esc(ucfirst($reportType ?? 'Report')) ?> Report</div>
        <div style="font-size:13px;color:#6b7280;">
            <?php if ($startDate || $endDate): ?>
                Period: <?= $startDate ? date('M d, Y', strtotime($startDate)) : 'All' ?> - <?= $endDate ? date('M d, Y', strtotime($endDate)) : 'All' ?>
            <?php else: ?>
                All Time
            <?php endif; ?>
        </div>
        <div style="font-size:13px;color:#6b7280;margin-top:4px;">
            Generated: <?= date('F d, Y \a\t h:i A') ?>
        </div>
    </div>

    <?php if (!empty($reportData)): ?>
        <?php if ($reportType === 'patients'): ?>
            <table>
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $patient): ?>
                        <tr>
                            <td>#<?= esc($patient['id']) ?></td>
                            <td><?= esc(trim(($patient['last_name'] ?? '') . ', ' . ($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? ''))) ?></td>
                            <td><?= $patient['date_of_birth'] ? date('M d, Y', strtotime($patient['date_of_birth'])) : 'N/A' ?></td>
                            <td><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></td>
                            <td><?= $patient['created_at'] ? date('M d, Y', strtotime($patient['created_at'])) : 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($reportType === 'appointments'): ?>
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $appt): ?>
                        <tr>
                            <td>#<?= esc($appt['id']) ?></td>
                            <td><?= esc(trim(($appt['last_name'] ?? '') . ', ' . ($appt['first_name'] ?? '') . ' ' . ($appt['middle_name'] ?? ''))) ?></td>
                            <td><?= $appt['appointment_date'] ? date('M d, Y', strtotime($appt['appointment_date'])) : 'N/A' ?></td>
                            <td><?= esc($appt['schedule_type'] ?? 'N/A') ?></td>
                            <td><?= esc(ucfirst($appt['status'] ?? 'pending')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($reportType === 'billing' || $reportType === 'revenue'): ?>
            <table>
                <thead>
                    <tr>
                        <th>Bill ID</th>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reportData as $bill): ?>
                        <tr>
                            <td>#<?= esc($bill['id']) ?></td>
                            <td><?= esc(trim(($bill['last_name'] ?? '') . ', ' . ($bill['first_name'] ?? '') . ' ' . ($bill['middle_name'] ?? ''))) ?></td>
                            <td><?= esc($bill['bill_type'] ?? 'N/A') ?></td>
                            <td>₱<?= number_format($bill['total_amount'] ?? 0, 2) ?></td>
                            <td><?= esc(ucfirst($bill['status'] ?? 'pending')) ?></td>
                            <td><?= $bill['created_at'] ? date('M d, Y', strtotime($bill['created_at'])) : 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php if ($reportType === 'revenue'): ?>
                <div style="margin-top:20px;padding-top:16px;border-top:2px solid #e5e7eb;display:flex;justify-content:space-between;font-weight:600;">
                    <span>Total Revenue:</span>
                    <span>₱<?= number_format(array_sum(array_column($reportData, 'total_amount')), 2) ?></span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php else: ?>
        <div style="text-align:center;padding:40px 20px;color:#6b7280;">
            <div style="font-size:48px;margin-bottom:12px;opacity:0.5;">📊</div>
            <div style="font-size:14px;font-weight:500;margin-bottom:4px;">No data found</div>
            <div style="font-size:12px;">No records match the selected criteria.</div>
        </div>
    <?php endif; ?>
</div>

<style>
@media print {
    .page-header { display: none; }
    .card { border: none; page-break-inside: avoid; }
}
</style>
<?= $this->endSection() ?>

