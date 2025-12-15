<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Hospital Reports</div>
        <div class="page-subtitle">View comprehensive reports and analytics for hospital operations.</div>
    </div>
</div>

<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Patients</div>
        </div>
        <div class="card-value"><?= number_format($totalPatients ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Active Doctors</div>
        </div>
        <div class="card-value" style="color:#0ea5e9;"><?= number_format($totalDoctors ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Appointments</div>
        </div>
        <div class="card-value"><?= number_format($totalAppointments ?? 0) ?></div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Revenue</div>
        </div>
        <div class="card-value" style="color:#16a34a;">₱<?= number_format($totalRevenue ?? 0, 2) ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size:14px;font-weight:600;">Generate Report</div>
    </div>
    <form action="/WebSys_HMS_G3/admin/reports/generate" method="post">
        <?= csrf_field() ?>
        <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
            <div>
                <label style="font-size:13px;margin-bottom:4px;display:block;font-weight:500;">Report Type *</label>
                <select name="report_type" required style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;">
                    <option value="">Select report type</option>
                    <option value="patients">Patient Report</option>
                    <option value="appointments">Appointments Report</option>
                    <option value="billing">Billing Report</option>
                    <option value="revenue">Revenue Report</option>
                </select>
            </div>
            <div>
                <label style="font-size:13px;margin-bottom:4px;display:block;font-weight:500;">Start Date</label>
                <input type="date" name="start_date" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;">
            </div>
            <div>
                <label style="font-size:13px;margin-bottom:4px;display:block;font-weight:500;">End Date</label>
                <input type="date" name="end_date" value="<?= date('Y-m-d') ?>" style="width:100%;padding:8px 12px;border-radius:8px;border:1px solid #d1d5db;font-size:13px;">
            </div>
        </div>
        <div style="margin-top:12px;display:flex;justify-content:flex-end;">
            <button type="submit" style="padding:8px 16px;border-radius:999px;border:none;background:#16a34a;color:#ffffff;font-size:13px;font-weight:600;cursor:pointer;">Generate Report</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size:14px;font-weight:600;">Monthly Statistics (Last 12 Months)</div>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Patients</th>
                    <th>Appointments</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($monthlyStats ?? []) as $stat): ?>
                    <tr>
                        <td><?= esc($stat['month']) ?></td>
                        <td><?= number_format($stat['patients']) ?></td>
                        <td><?= number_format($stat['appointments']) ?></td>
                        <td>₱<?= number_format($stat['revenue'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size:14px;font-weight:600;">Recent Patients</div>
    </div>
    <?php if (!empty($recentPatients)): ?>
        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Name</th>
                    <th>Registered Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentPatients as $patient): ?>
                    <tr>
                        <td>#<?= esc($patient['id']) ?></td>
                        <td><?= esc(trim(($patient['last_name'] ?? '') . ', ' . ($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? ''))) ?></td>
                        <td><?= $patient['created_at'] ? date('M d, Y', strtotime($patient['created_at'])) : 'N/A' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center;padding:20px;color:#6b7280;font-size:13px;">No recent patients</div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title" style="font-size:14px;font-weight:600;">Recent Appointments</div>
    </div>
    <?php if (!empty($recentAppointments)): ?>
        <table>
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient</th>
                    <th>Doctor</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentAppointments as $appt): ?>
                    <tr>
                        <td>#<?= esc($appt['id']) ?></td>
                        <td><?= esc(trim(($appt['last_name'] ?? '') . ', ' . ($appt['first_name'] ?? '') . ' ' . ($appt['middle_name'] ?? ''))) ?></td>
                        <td><?= esc($appt['doctor_name'] ?? 'N/A') ?></td>
                        <td><?= $appt['appointment_date'] ? date('M d, Y', strtotime($appt['appointment_date'])) : 'N/A' ?></td>
                        <td><?= esc(ucfirst($appt['status'] ?? 'pending')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align:center;padding:20px;color:#6b7280;font-size:13px;">No recent appointments</div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>

