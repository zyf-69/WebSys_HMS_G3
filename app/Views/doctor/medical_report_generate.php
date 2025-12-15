<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header" style="margin-bottom: 24px;">
    <div>
        <div class="page-title">Medical Report</div>
        <div class="page-subtitle">Generated medical report for patient</div>
    </div>
    <div>
        <a href="/WebSys_HMS_G3/doctor/medical-reports" class="btn-secondary" style="text-decoration: none; display: inline-block;">← Back</a>
        <button onclick="window.print()" class="btn-primary" style="margin-left: 8px;">Print Report</button>
    </div>
</div>

<style>
    @media print {
        .page-header, .btn-secondary, .btn-primary {
            display: none;
        }
    }
    .report-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 32px;
        margin-bottom: 24px;
    }
    .report-header {
        text-align: center;
        border-bottom: 3px solid #111827;
        padding-bottom: 20px;
        margin-bottom: 32px;
    }
    .hospital-name {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }
    .report-title {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin-top: 16px;
    }
    .patient-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 32px;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
    }
    .info-item {
        font-size: 13px;
    }
    .info-label {
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .info-value {
        color: #111827;
    }
    .report-section {
        margin-bottom: 32px;
        page-break-inside: avoid;
    }
    .section-title {
        font-size: 16px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e5e7eb;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .section-content {
        font-size: 13px;
        color: #374151;
        line-height: 1.8;
        white-space: pre-wrap;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        margin-top: 12px;
    }
    .data-table th {
        background: #f3f4f6;
        padding: 8px 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    .data-table td {
        padding: 8px 12px;
        border: 1px solid #e5e7eb;
        color: #111827;
    }
    .data-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }
    .signature-section {
        margin-top: 48px;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 32px;
    }
    .signature-box {
        border-top: 2px solid #111827;
        padding-top: 12px;
        text-align: center;
    }
    .signature-name {
        font-weight: 600;
        color: #111827;
        margin-top: 8px;
    }
    .signature-title {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
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
        text-decoration: none;
        display: inline-block;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
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
</style>

<div class="report-card">
    <div class="report-header">
        <div class="hospital-name">HMS System</div>
        <div style="font-size: 14px; color: #6b7280;">St. Peter Hospital</div>
        <div class="report-title">MEDICAL REPORT</div>
    </div>

    <div class="patient-info">
        <div class="info-item">
            <div class="info-label">Patient Name</div>
            <div class="info-value">
                <?php
                $patientName = trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''));
                echo esc($patientName ?: 'N/A');
                ?>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Patient Code</div>
            <div class="info-value"><?= esc($patient['patient_code'] ?? 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Date of Birth</div>
            <div class="info-value"><?= esc($patient['date_of_birth'] ? date('F d, Y', strtotime($patient['date_of_birth'])) : 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Age</div>
            <div class="info-value"><?= esc($patient['age'] ?? 'N/A') ?> years</div>
        </div>
        <div class="info-item">
            <div class="info-label">Gender</div>
            <div class="info-value"><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Blood Type</div>
            <div class="info-value"><?= esc($patient['blood_type'] ?? 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Contact Number</div>
            <div class="info-value"><?= esc($patient['mobile_number'] ?? 'N/A') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value"><?= esc($patient['email'] ?? 'N/A') ?></div>
        </div>
    </div>

    <?php if (!empty($patient['blood_pressure']) || !empty($patient['heart_rate']) || !empty($patient['temperature'])): ?>
    <div class="report-section">
        <div class="section-title">Vital Signs</div>
        <div class="section-content">
            <?php if (!empty($patient['blood_pressure'])): ?>
                <strong>Blood Pressure:</strong> <?= esc($patient['blood_pressure']) ?> mmHg<br>
            <?php endif; ?>
            <?php if (!empty($patient['heart_rate'])): ?>
                <strong>Heart Rate:</strong> <?= esc($patient['heart_rate']) ?> bpm<br>
            <?php endif; ?>
            <?php if (!empty($patient['temperature'])): ?>
                <strong>Temperature:</strong> <?= esc($patient['temperature']) ?> °C<br>
            <?php endif; ?>
            <?php if (!empty($patient['height_cm']) && !empty($patient['weight_kg'])): ?>
                <strong>Height:</strong> <?= esc($patient['height_cm']) ?> cm | 
                <strong>Weight:</strong> <?= esc($patient['weight_kg']) ?> kg | 
                <strong>BMI:</strong> <?= esc($patient['bmi'] ?? 'N/A') ?><br>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($appointments)): ?>
    <div class="report-section">
        <div class="section-title">Appointment History</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Shift</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appointment): ?>
                    <tr>
                        <td><?= esc($appointment['appointment_date'] ? date('M d, Y', strtotime($appointment['appointment_date'])) : 'N/A') ?></td>
                        <td><?= esc($appointment['start_time'] ? date('g:i A', strtotime($appointment['start_time'])) : 'N/A') ?></td>
                        <td><?= esc($appointment['shift_name'] ?? 'N/A') ?></td>
                        <td><?= esc(ucfirst($appointment['status'] ?? 'N/A')) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if (!empty($prescriptions)): ?>
    <div class="report-section">
        <div class="section-title">Prescription History</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td><?= esc($prescription['medicine_name'] ?? 'N/A') ?></td>
                        <td><?= esc($prescription['prescribed_quantity'] ?? 0) ?> <?= esc($prescription['unit'] ?? '') ?></td>
                        <td><?= esc($prescription['prescription_date'] ? date('M d, Y', strtotime($prescription['prescription_date'])) : 'N/A') ?></td>
                        <td><?= esc(ucfirst(str_replace('_', ' ', $prescription['status'] ?? 'pending'))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if (!empty($labTests)): ?>
    <div class="report-section">
        <div class="section-title">Laboratory Tests</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Test Name</th>
                    <th>Test Type</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($labTests as $test): ?>
                    <tr>
                        <td><?= esc($test['test_name'] ?? 'N/A') ?></td>
                        <td><?= esc($test['test_type'] ?? 'N/A') ?></td>
                        <td><?= esc($test['created_at'] ? date('M d, Y', strtotime($test['created_at'])) : 'N/A') ?></td>
                        <td><?= esc(ucfirst(str_replace('_', ' ', $test['status'] ?? 'pending'))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if (!empty($patient['medical_notes'])): ?>
    <div class="report-section">
        <div class="section-title">Medical Notes</div>
        <div class="section-content"><?= esc($patient['medical_notes']) ?></div>
    </div>
    <?php endif; ?>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-name"><?= esc($doctor['full_name'] ?? 'Dr. ' . ($doctor['first_name'] ?? '') . ' ' . ($doctor['last_name'] ?? '')) ?></div>
            <div class="signature-title">Attending Physician</div>
            <div class="signature-title"><?= esc($doctor['specialization'] ?? '') ?></div>
        </div>
        <div class="signature-box">
            <div class="signature-name" style="margin-top: 40px;">&nbsp;</div>
            <div class="signature-title">Date: <?= date('F d, Y') ?></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

