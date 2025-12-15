<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Admin Dashboard</div>
        <div class="page-subtitle">Overview of hospital operations and HMS activity.</div>
    </div>
    <div class="page-subtitle">
        <?= date('l, d M Y') ?>
    </div>
</div>

<div class="grid grid-4" style="margin-bottom: 16px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Total Patients</div>
        </div>
        <div class="card-value">--</div>
        <div class="card-trend">+0 today</div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">On-duty Doctors</div>
        </div>
        <div class="card-value">--</div>
        <div class="card-trend">Schedule synced</div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Nurses Assigned</div>
        </div>
        <div class="card-value">--</div>
        <div class="card-trend">Wards balanced</div>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">Todays Appointments</div>
        </div>
        <div class="card-value">--</div>
        <div class="card-trend">Clinic activity</div>
    </div>
</div>

<div class="grid" style="grid-template-columns: minmax(0, 1fr); gap: 14px; margin-bottom: 16px;">
    <div class="card" style="cursor: pointer; transition: all 0.2s ease;" onclick="window.location.href='<?= base_url('patients/records') ?>'" onmouseover="this.style.borderColor='#16a34a'; this.style.boxShadow='0 2px 8px rgba(22, 163, 74, 0.15)'" onmouseout="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
        <div class="card-header" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="card-title" style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">Patient Records</div>
                <div style="font-size: 12px; color: #6b7280;">View and manage all registered patient records</div>
            </div>
            <div style="font-size: 24px;">📋</div>
        </div>
        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb;">
            <a href="<?= base_url('patients/records') ?>" style="color: #16a34a; text-decoration: none; font-size: 13px; font-weight: 500;">
                View All Records →
            </a>
        </div>
    </div>
</div>

<div class="grid" style="grid-template-columns: minmax(0, 1.6fr) minmax(0, 1fr); gap: 14px;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent System Activity</div>
        </div>
        <table>
            <thead>
            <tr>
                <th>Time</th>
                <th>User</th>
                <th>Action</th>
                <th>Module</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>--</td>
                <td>--</td>
                <td>Login</td>
                <td>Authentication</td>
            </tr>
            <tr>
                <td>--</td>
                <td>--</td>
                <td>Updated patient record</td>
                <td>Patients</td>
            </tr>
            <tr>
                <td>--</td>
                <td>--</td>
                <td>Scheduled appointment</td>
                <td>Scheduling</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="card-title">System Status</div>
        </div>
        <table>
            <tbody>
            <tr>
                <th>Environment</th>
                <td>Development</td>
            </tr>
            <tr>
                <th>Application</th>
                <td>HMS System</td>
            </tr>
            <tr>
                <th>Hospital</th>
                <td>St. Peter Hospital</td>
            </tr>
            <tr>
                <th>Logged in as</th>
                <td><?= esc(session()->get('email') ?? 'admin@example.com') ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
