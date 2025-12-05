<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Patient Registration &amp; EHR</div>
        <div class="page-subtitle">Register inpatient and outpatient records in a unified electronic health record form.</div>
    </div>
</div>

<style>
    .patient-form-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 16px 16px 18px;
    }
    .patient-form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px 14px;
    }
    .patient-section-title {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .form-group-inline {
        display: flex;
        flex-direction: column;
        font-size: 13px;
    }
    .form-group-inline label {
        margin-bottom: 3px;
    }
    .form-group-inline input,
    .form-group-inline select,
    .form-group-inline textarea {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 6px 8px;
        font-size: 13px;
    }
    .visit-type-toggle {
        display: inline-flex;
        border-radius: 999px;
        background: #e5e7eb;
        padding: 2px;
        margin-bottom: 10px;
    }
    .visit-type-btn {
        border: none;
        background: transparent;
        padding: 6px 14px;
        font-size: 13px;
        border-radius: 999px;
        cursor: pointer;
    }
    .visit-type-btn.active {
        background: #16a34a;
        color: #ffffff;
    }
    .admission-section {
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px dashed #d1d5db;
    }
</style>

<?php $patients = $patients ?? []; ?>

<div class="patient-form-card" style="margin-bottom:16px;">
    <form action="<?= base_url('patients/register') ?>" method="post" id="patient-registration-form">
        <?= csrf_field() ?>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <div>
                <div class="patient-section-title">Visit Type</div>
                <div class="visit-type-toggle">
                    <button type="button" class="visit-type-btn active" data-type="inpatient">Inpatient</button>
                    <button type="button" class="visit-type-btn" data-type="outpatient">Outpatient</button>
                </div>
                <input type="hidden" name="visit_type" id="visit_type" value="inpatient">
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Personal Information</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                </div>
                <div class="form-group-inline">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name">
                </div>
                <div class="form-group-inline">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                </div>
                <div class="form-group-inline">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth">
                </div>
                <div class="form-group-inline">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="civil_status">Civil Status</label>
                    <input type="text" id="civil_status" name="civil_status">
                </div>
                <div class="form-group-inline">
                    <label for="place_of_birth">Place of Birth</label>
                    <input type="text" id="place_of_birth" name="place_of_birth">
                </div>
                <div class="form-group-inline">
                    <label for="blood_type">Blood Type</label>
                    <input type="text" id="blood_type" name="blood_type" placeholder="e.g. O+">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Contact Information</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="province">Province</label>
                    <input type="text" id="province" name="province">
                </div>
                <div class="form-group-inline">
                    <label for="city_municipality">City / Municipality</label>
                    <input type="text" id="city_municipality" name="city_municipality">
                </div>
                <div class="form-group-inline">
                    <label for="barangay">Barangay</label>
                    <input type="text" id="barangay" name="barangay">
                </div>
                <div class="form-group-inline">
                    <label for="street">Street</label>
                    <input type="text" id="street" name="street">
                </div>
                <div class="form-group-inline">
                    <label for="phone_number">Telephone Number</label>
                    <input type="text" id="phone_number" name="phone_number">
                </div>
                <div class="form-group-inline">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number">
                </div>
                <div class="form-group-inline">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Emergency Contact</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="emergency_contact_person">Contact Person</label>
                    <input type="text" id="emergency_contact_person" name="emergency_contact_person">
                </div>
                <div class="form-group-inline">
                    <label for="emergency_relationship">Relationship</label>
                    <input type="text" id="emergency_relationship" name="emergency_relationship">
                </div>
                <div class="form-group-inline">
                    <label for="emergency_contact_number">Contact Number</label>
                    <input type="text" id="emergency_contact_number" name="emergency_contact_number">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Vitals</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="blood_pressure">Blood Pressure</label>
                    <input type="text" id="blood_pressure" name="blood_pressure" placeholder="e.g. 120/80">
                </div>
                <div class="form-group-inline">
                    <label for="heart_rate">Heart Rate (bpm)</label>
                    <input type="number" id="heart_rate" name="heart_rate">
                </div>
                <div class="form-group-inline">
                    <label for="temperature">Temperature (°C)</label>
                    <input type="number" step="0.1" id="temperature" name="temperature">
                </div>
                <div class="form-group-inline">
                    <label for="height_cm">Height (cm)</label>
                    <input type="number" step="0.1" id="height_cm" name="height_cm">
                </div>
                <div class="form-group-inline">
                    <label for="weight_kg">Weight (kg)</label>
                    <input type="number" step="0.1" id="weight_kg" name="weight_kg">
                </div>
                <div class="form-group-inline">
                    <label for="bmi">BMI</label>
                    <input type="number" step="0.1" id="bmi" name="bmi" readonly>
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Insurance Information</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="insurance_provider">Insurance Provider</label>
                    <input type="text" id="insurance_provider" name="insurance_provider">
                </div>
                <div class="form-group-inline">
                    <label for="insurance_contact_number">Insurance Contact Number</label>
                    <input type="text" id="insurance_contact_number" name="insurance_contact_number">
                </div>
                <div class="form-group-inline">
                    <label for="policy_number">Policy Number</label>
                    <input type="text" id="policy_number" name="policy_number">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Medical Notes</div>
            <div class="form-group-inline">
                <textarea id="medical_notes" name="medical_notes" rows="3"></textarea>
            </div>
        </div>

        <div id="admission-section" class="admission-section">
            <div class="patient-section-title">Admission Details (Inpatient Only)</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="admission_date">Admission Date</label>
                    <input type="date" id="admission_date" name="admission_date">
                </div>
                <div class="form-group-inline">
                    <label for="admission_time">Admission Time</label>
                    <input type="time" id="admission_time" name="admission_time">
                </div>
                <div class="form-group-inline">
                    <label for="room_type">Room Type</label>
                    <input type="text" id="room_type" name="room_type">
                </div>
                <div class="form-group-inline">
                    <label for="room_number">Room Number</label>
                    <input type="text" id="room_number" name="room_number">
                </div>
                <div class="form-group-inline">
                    <label for="bed_number">Bed Number</label>
                    <input type="text" id="bed_number" name="bed_number">
                </div>
            </div>
        </div>

        <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:8px;">
            <button type="reset" style="padding:7px 14px;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;font-size:13px;cursor:pointer;">Clear</button>
            <button type="submit" style="padding:7px 16px;border-radius:999px;border:none;background:#16a34a;color:#ffffff;font-size:13px;font-weight:600;cursor:pointer;">Save Patient Record</button>
        </div>
    </form>
</div>

<?php if (! empty($patients)): ?>
    <div class="patient-form-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <div class="patient-section-title">Patient Records</div>
            <div style="display:inline-flex;border-radius:999px;background:#e5e7eb;padding:2px;">
                <button type="button" class="visit-type-btn active" data-filter="all">All</button>
                <button type="button" class="visit-type-btn" data-filter="inpatient">Inpatient</button>
                <button type="button" class="visit-type-btn" data-filter="outpatient">Outpatient</button>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                <thead>
                <tr>
                    <th style="text-align:left;padding:6px 8px;border-bottom:1px solid #e5e7eb;">MRN / ID</th>
                    <th style="text-align:left;padding:6px 8px;border-bottom:1px solid #e5e7eb;">Name</th>
                    <th style="text-align:left;padding:6px 8px;border-bottom:1px solid #e5e7eb;">Date of Birth</th>
                    <th style="text-align:left;padding:6px 8px;border-bottom:1px solid #e5e7eb;">Gender</th>
                    <th style="text-align:left;padding:6px 8px;border-bottom:1px solid #e5e7eb;">Visit Type</th>
                </tr>
                </thead>
                <tbody id="patient-records-body">
                <?php foreach ($patients as $row): ?>
                    <?php
                    $fullName = trim(($row['last_name'] ?? '') . ', ' . ($row['first_name'] ?? '') . ' ' . ($row['middle_name'] ?? ''));
                    $dob = $row['date_of_birth'] ?? '';
                    $gender = $row['gender'] ?? '';
                    $visitType = $row['visit_type'] ?? 'outpatient';
                    ?>
                    <tr data-visit-type="<?= esc($visitType) ?>">
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;">#<?= esc($row['id']) ?></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;"><?= esc($fullName) ?></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;"><?= esc($dob) ?></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;"><?= esc(ucfirst($gender)) ?></td>
                        <td style="padding:6px 8px;border-bottom:1px solid #f3f4f6;"><?= esc(ucfirst($visitType)) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<script>
    (function() {
        const inpatientBtn = document.querySelector('.visit-type-btn[data-type="inpatient"]');
        const outpatientBtn = document.querySelector('.visit-type-btn[data-type="outpatient"]');
        const visitTypeInput = document.getElementById('visit_type');
        const admissionSection = document.getElementById('admission-section');
        const heightInput = document.getElementById('height_cm');
        const weightInput = document.getElementById('weight_kg');
        const bmiInput = document.getElementById('bmi');

        function setVisitType(type) {
            visitTypeInput.value = type;
            if (type === 'inpatient') {
                inpatientBtn.classList.add('active');
                outpatientBtn.classList.remove('active');
                admissionSection.style.display = '';
            } else {
                outpatientBtn.classList.add('active');
                inpatientBtn.classList.remove('active');
                admissionSection.style.display = 'none';
            }
        }

        if (inpatientBtn && outpatientBtn) {
            inpatientBtn.addEventListener('click', function () {
                setVisitType('inpatient');
            });

            outpatientBtn.addEventListener('click', function () {
                setVisitType('outpatient');
            });
        }

        function updateBMI() {
            const h = parseFloat(heightInput.value || '0');
            const w = parseFloat(weightInput.value || '0');
            if (h > 0 && w > 0) {
                const hMeters = h / 100.0;
                const bmi = w / (hMeters * hMeters);
                bmiInput.value = bmi.toFixed(1);
            } else {
                bmiInput.value = '';
            }
        }

        heightInput.addEventListener('input', updateBMI);
        weightInput.addEventListener('input', updateBMI);

        // Patient list filters
        const recordFilterButtons = document.querySelectorAll('.patient-form-card .visit-type-btn[data-filter]');
        const recordRows = document.querySelectorAll('#patient-records-body tr');

        function setRecordFilter(filter) {
            recordFilterButtons.forEach(btn => {
                if (btn.getAttribute('data-filter') === filter) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            recordRows.forEach(row => {
                const type = row.getAttribute('data-visit-type');
                if (filter === 'all' || filter === type) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        recordFilterButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const filter = this.getAttribute('data-filter');
                setRecordFilter(filter);
            });
        });

        // defaults
        setVisitType('inpatient');
        setRecordFilter('all');
    })();
</script>

<?= $this->endSection() ?>
