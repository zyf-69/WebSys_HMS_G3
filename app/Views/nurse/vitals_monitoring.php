<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Vitals Monitoring</div>
        <div class="page-subtitle">Monitor and update vital signs for assigned patients.</div>
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
    .search-container {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px;
        margin-bottom: 20px;
    }
    .search-input {
        width: 100%;
        padding: 10px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    .search-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .vitals-table-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        overflow-x: auto;
    }
    .vitals-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .vitals-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .vitals-table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .vitals-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        color: #111827;
        vertical-align: middle;
    }
    .vitals-table tbody tr:hover {
        background: #f9fafb;
    }
    .vitals-table tbody tr:last-child td {
        border-bottom: none;
    }
    .patient-name-cell {
        font-weight: 600;
        color: #111827;
    }
    .patient-meta {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .badge-room {
        display: inline-block;
        padding: 3px 8px;
        background: #dbeafe;
        color: #1e40af;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    .vital-input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        min-width: 80px;
    }
    .vital-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    .vital-input:read-only {
        background: #f3f4f6;
        cursor: not-allowed;
    }
    .btn-update-row {
        background: #10b981;
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }
    .btn-update-row:hover {
        background: #059669;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 16px;
    }
    .empty-state-title {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 8px;
        color: #374151;
    }
</style>

<div class="search-container">
    <input type="text" class="search-input" id="searchInput" placeholder="Search by patient name or room number...">
</div>

<?php if (empty($patients)): ?>
    <div class="vitals-table-card">
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <div class="empty-state-title">No patients found</div>
            <div>Patients assigned to your ward will appear here.</div>
        </div>
    </div>
<?php else: ?>
    <div class="vitals-table-card">
        <form action="/WebSys_HMS_G3/nurse/vitals-monitoring" method="post" id="vitalsForm">
            <?= csrf_field() ?>
            <table class="vitals-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Room/Bed</th>
                        <th>Blood Pressure</th>
                        <th>Heart Rate</th>
                        <th>Temperature</th>
                        <th>Height</th>
                        <th>Weight</th>
                        <th>BMI</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr data-search="<?= strtolower(esc($patient['full_name'] . ' ' . ($patient['room_number'] ?? '') . ' ' . ($patient['bed_number'] ?? ''))) ?>">
                            <td class="patient-name-cell">
                                <div><?= esc($patient['full_name'] ?? 'N/A') ?></div>
                                <div class="patient-meta">
                                    <?php if (!empty($patient['patient_code'])): ?>
                                        ID: <?= esc($patient['patient_code']) ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($patient['room_number']) || !empty($patient['bed_number'])): ?>
                                    <span class="badge-room">
                                        <?= esc($patient['room_number'] ?? 'N/A') ?> / <?= esc($patient['bed_number'] ?? 'N/A') ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <input type="text" name="vitals[<?= esc($patient['id']) ?>][blood_pressure]" 
                                       class="vital-input" placeholder="120/80" 
                                       value="<?= esc($patient['blood_pressure'] ?? '') ?>">
                            </td>
                            <td>
                                <input type="number" name="vitals[<?= esc($patient['id']) ?>][heart_rate]" 
                                       class="vital-input" placeholder="72" 
                                       value="<?= esc($patient['heart_rate'] ?? '') ?>" min="0">
                            </td>
                            <td>
                                <input type="number" name="vitals[<?= esc($patient['id']) ?>][temperature]" 
                                       class="vital-input" placeholder="36.5" 
                                       value="<?= esc($patient['temperature'] ?? '') ?>" step="0.1" min="0">
                            </td>
                            <td>
                                <input type="number" name="vitals[<?= esc($patient['id']) ?>][height_cm]" 
                                       class="vital-input" placeholder="170" 
                                       value="<?= esc($patient['height_cm'] ?? '') ?>" step="0.1" min="0"
                                       oninput="calculateBMI(<?= esc($patient['id']) ?>)">
                            </td>
                            <td>
                                <input type="number" name="vitals[<?= esc($patient['id']) ?>][weight_kg]" 
                                       class="vital-input" placeholder="70" 
                                       value="<?= esc($patient['weight_kg'] ?? '') ?>" step="0.1" min="0"
                                       oninput="calculateBMI(<?= esc($patient['id']) ?>)">
                            </td>
                            <td>
                                <input type="text" name="vitals[<?= esc($patient['id']) ?>][bmi]" 
                                       class="vital-input" id="bmi_<?= esc($patient['id']) ?>" 
                                       value="<?= esc($patient['bmi'] ?? '') ?>" readonly>
                            </td>
                            <td>
                                <button type="button" class="btn-update-row" 
                                        onclick="updatePatientVitals(<?= esc($patient['id']) ?>)">
                                    Update
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>
    </div>
<?php endif; ?>

<script>
    // Search functionality
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.vitals-table tbody tr');
        
        rows.forEach(row => {
            const searchText = row.getAttribute('data-search') || '';
            row.style.display = searchText.includes(searchTerm) ? '' : 'none';
        });
    });

    // Calculate BMI
    function calculateBMI(patientId) {
        const heightInput = document.querySelector(`input[name="vitals[${patientId}][height_cm]"]`);
        const weightInput = document.querySelector(`input[name="vitals[${patientId}][weight_kg]"]`);
        const bmiInput = document.getElementById(`bmi_${patientId}`);
        
        if (heightInput && weightInput && bmiInput) {
            const height = parseFloat(heightInput.value);
            const weight = parseFloat(weightInput.value);
            
            if (height > 0 && weight > 0) {
                const heightM = height / 100;
                const bmi = (weight / (heightM * heightM)).toFixed(1);
                bmiInput.value = bmi;
            } else {
                bmiInput.value = '';
            }
        }
    }

    // Update single patient vitals
    function updatePatientVitals(patientId) {
        const form = document.getElementById('vitalsForm');
        const formData = new FormData();
        
        // Get CSRF token
        const csrfInput = form.querySelector('input[name="csrf_test_name"]');
        if (csrfInput) {
            formData.append('csrf_test_name', csrfInput.value);
        }
        
        // Add patient ID
        formData.append('patient_id', patientId);
        
        // Get all vitals for this patient
        const bloodPressure = document.querySelector(`input[name="vitals[${patientId}][blood_pressure]"]`).value;
        const heartRate = document.querySelector(`input[name="vitals[${patientId}][heart_rate]"]`).value;
        const temperature = document.querySelector(`input[name="vitals[${patientId}][temperature]"]`).value;
        const heightCm = document.querySelector(`input[name="vitals[${patientId}][height_cm]"]`).value;
        const weightKg = document.querySelector(`input[name="vitals[${patientId}][weight_kg]"]`).value;
        const bmi = document.getElementById(`bmi_${patientId}`).value;
        
        formData.append('blood_pressure', bloodPressure);
        formData.append('heart_rate', heartRate);
        formData.append('temperature', temperature);
        formData.append('height_cm', heightCm);
        formData.append('weight_kg', weightKg);
        formData.append('bmi', bmi);
        
        // Submit via fetch
        fetch('/WebSys_HMS_G3/nurse/vitals-monitoring', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update vitals. Please try again.');
        });
    }
</script>

<?= $this->endSection() ?>
