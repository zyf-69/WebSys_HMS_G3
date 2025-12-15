<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Lab Results</div>
        <div class="page-subtitle">View and manage laboratory test results for your patients.</div>
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
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .badge-in-progress {
        background: #dbeafe;
        color: #1e40af;
    }
    .badge-completed {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-cancelled {
        background: #fee2e2;
        color: #991b1b;
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
    .form-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        margin-bottom: 24px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        color: #111827;
        margin-bottom: 6px;
    }
    .form-label .required {
        color: #dc2626;
    }
    .form-input, .form-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        color: #111827;
    }
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        color: #111827;
        min-height: 80px;
        resize: vertical;
    }
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
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
    .btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
    }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Tests</div>
        <div class="stat-value"><?= esc($totalTests ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending</div>
        <div class="stat-value"><?= esc($pendingTests ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">In Progress</div>
        <div class="stat-value"><?= esc($inProgressTests ?? 0) ?></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed</div>
        <div class="stat-value"><?= esc($completedTests ?? 0) ?></div>
    </div>
</div>

<div class="form-card">
    <h3 style="margin-top: 0; margin-bottom: 20px; font-size: 16px; font-weight: 600; color: #111827;">Create New Lab Request</h3>
    <form action="/WebSys_HMS_G3/doctor/lab-results" method="post">
        <?= csrf_field() ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            <div class="form-group">
                <label class="form-label">Patient <span class="required">*</span></label>
                <select name="patient_id" class="form-select" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients ?? [] as $patient): ?>
                        <option value="<?= esc($patient['id']) ?>">
                            <?= esc(trim(($patient['first_name'] ?? '') . ' ' . ($patient['middle_name'] ?? '') . ' ' . ($patient['last_name'] ?? ''))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Test Type <span class="required">*</span></label>
                <select name="test_type" class="form-select" required>
                    <option value="">Select Test Type</option>
                    <option value="Blood Test">Blood Test</option>
                    <option value="Urine Test">Urine Test</option>
                    <option value="X-Ray">X-Ray</option>
                    <option value="CT Scan">CT Scan</option>
                    <option value="MRI">MRI</option>
                    <option value="Ultrasound">Ultrasound</option>
                    <option value="ECG">ECG</option>
                    <option value="Biopsy">Biopsy</option>
                    <option value="Culture">Culture</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Test Name <span class="required">*</span></label>
                <select name="test_name" id="test_name" class="form-select" required>
                    <option value="">Select Test Type First</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Notes</label>
            <textarea name="notes" class="form-textarea" placeholder="Additional notes or instructions for the lab staff..."></textarea>
        </div>
        <div class="form-actions">
            <button type="button" class="btn-secondary" onclick="this.form.reset()">Clear</button>
            <button type="submit" class="btn-primary">Create Lab Request</button>
        </div>
    </form>
</div>

<div class="records-card">
    <table class="records-table">
        <thead>
            <tr>
                <th>Test Name</th>
                <th>Test Type</th>
                <th>Patient</th>
                <th>Status</th>
                <th>Date Created</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($labTests)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon">⚗</div>
                        <div>No lab tests found</div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($labTests as $test): ?>
                    <tr>
                        <td><strong><?= esc($test['test_name'] ?? 'N/A') ?></strong></td>
                        <td><?= esc($test['test_type'] ?? 'N/A') ?></td>
                        <td>
                            <?php
                            $patientName = trim(($test['first_name'] ?? '') . ' ' . ($test['middle_name'] ?? '') . ' ' . ($test['last_name'] ?? ''));
                            echo esc($patientName ?: 'N/A');
                            ?>
                        </td>
                        <td>
                            <?php
                            $status = $test['status'] ?? 'pending';
                            $statusClass = 'badge-' . str_replace('_', '-', $status);
                            ?>
                            <span class="badge <?= esc($statusClass) ?>"><?= esc(ucfirst(str_replace('_', ' ', $status))) ?></span>
                        </td>
                        <td><?= esc($test['created_at'] ? date('M d, Y', strtotime($test['created_at'])) : 'N/A') ?></td>
                        <td><?= esc($test['notes'] ? substr($test['notes'], 0, 50) . (strlen($test['notes']) > 50 ? '...' : '') : '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Define test names for each test type
    const testNamesByType = {
        'Blood Test': [
            'Complete Blood Count (CBC)',
            'Blood Glucose (Fasting)',
            'Lipid Profile',
            'Liver Function Test (LFT)',
            'Kidney Function Test (KFT)',
            'Thyroid Function Test (TFT)',
            'Hemoglobin A1C',
            'Blood Group & Rh Factor',
            'Coagulation Profile',
            'Erythrocyte Sedimentation Rate (ESR)',
            'C-Reactive Protein (CRP)',
            'Vitamin D',
            'Vitamin B12',
            'Iron Studies',
            'Hormone Panel'
        ],
        'Urine Test': [
            'Urinalysis (Complete)',
            'Urine Culture & Sensitivity',
            'Urine Microalbumin',
            '24-Hour Urine Collection',
            'Pregnancy Test (Urine)',
            'Drug Screening (Urine)'
        ],
        'X-Ray': [
            'Chest X-Ray',
            'Abdominal X-Ray',
            'Skull X-Ray',
            'Spine X-Ray',
            'Pelvis X-Ray',
            'Extremity X-Ray',
            'Dental X-Ray',
            'Mammography'
        ],
        'CT Scan': [
            'CT Head',
            'CT Chest',
            'CT Abdomen & Pelvis',
            'CT Angiography',
            'CT Spine',
            'CT Sinus',
            'CT Coronary Angiography'
        ],
        'MRI': [
            'MRI Brain',
            'MRI Spine',
            'MRI Joint (Knee/Shoulder)',
            'MRI Abdomen',
            'MRI Pelvis',
            'MRA (Magnetic Resonance Angiography)'
        ],
        'Ultrasound': [
            'Abdominal Ultrasound',
            'Pelvic Ultrasound',
            'Obstetric Ultrasound',
            'Transvaginal Ultrasound',
            'Thyroid Ultrasound',
            'Breast Ultrasound',
            'Doppler Ultrasound',
            'Echocardiography'
        ],
        'ECG': [
            'Electrocardiogram (ECG/EKG)',
            'Stress Test (Treadmill)',
            'Holter Monitor (24-Hour)',
            'Echocardiogram'
        ],
        'Biopsy': [
            'Tissue Biopsy',
            'Bone Marrow Biopsy',
            'Liver Biopsy',
            'Kidney Biopsy',
            'Lymph Node Biopsy',
            'Skin Biopsy'
        ],
        'Culture': [
            'Blood Culture',
            'Urine Culture',
            'Sputum Culture',
            'Wound Culture',
            'Stool Culture',
            'Throat Culture'
        ],
        'Other': [
            'Stool Test',
            'Sputum Test',
            'Tuberculosis Test (TB)',
            'HIV Test',
            'Hepatitis Panel',
            'Pap Smear',
            'Semen Analysis',
            'Other (Specify in Notes)'
        ]
    };

    const testTypeSelect = document.querySelector('select[name="test_type"]');
    const testNameSelect = document.getElementById('test_name');

    // Function to populate test names based on selected test type
    function updateTestNames() {
        const selectedType = testTypeSelect.value;
        
        // Clear existing options
        testNameSelect.innerHTML = '';
        
        if (!selectedType) {
            // No test type selected
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Select Test Type First';
            testNameSelect.appendChild(option);
            testNameSelect.disabled = true;
        } else {
            // Enable the dropdown
            testNameSelect.disabled = false;
            
            // Add "Select Test Name" as first option
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = 'Select Test Name';
            testNameSelect.appendChild(defaultOption);
            
            // Add test names for the selected type
            if (testNamesByType[selectedType]) {
                testNamesByType[selectedType].forEach(function(testName) {
                    const option = document.createElement('option');
                    option.value = testName;
                    option.textContent = testName;
                    testNameSelect.appendChild(option);
                });
            } else {
                // Fallback if test type not found
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'No tests available for this type';
                testNameSelect.appendChild(option);
            }
        }
    }

    // Add event listener to test type dropdown
    if (testTypeSelect) {
        testTypeSelect.addEventListener('change', updateTestNames);
        
        // Initialize on page load if a test type is already selected
        if (testTypeSelect.value) {
            updateTestNames();
        }
    }
});
</script>

<?= $this->endSection() ?>

