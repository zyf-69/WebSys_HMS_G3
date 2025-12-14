<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Edit Patient Record</div>
        <div class="page-subtitle">Update patient information and electronic health record.</div>
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
    .outpatient-hidden {
        /* Will be controlled by JavaScript based on visit type */
    }
</style>

<?php 
$p = $patient ?? [];
$visitType = $p['visit_type'] ?? 'inpatient';
?>

<div class="patient-form-card" style="margin-bottom:16px;">
    <form action="<?= base_url('patients/update/' . $p['id']) ?>" method="post" id="patient-edit-form">
        <?= csrf_field() ?>

        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            <div>
                <div class="patient-section-title">Visit Type</div>
                <div class="visit-type-toggle">
                    <button type="button" class="visit-type-btn <?= $visitType === 'inpatient' ? 'active' : '' ?>" data-type="inpatient">Inpatient</button>
                    <button type="button" class="visit-type-btn <?= $visitType === 'outpatient' ? 'active' : '' ?>" data-type="outpatient">Outpatient</button>
                </div>
                <input type="hidden" name="visit_type" id="visit_type" value="<?= esc($visitType) ?>">
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Personal Information</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= esc($p['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group-inline">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" value="<?= esc($p['middle_name'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= esc($p['last_name'] ?? '') ?>" required>
                </div>
                <div class="form-group-inline">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" value="<?= esc($p['date_of_birth'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select</option>
                        <option value="male" <?= ($p['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= ($p['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                        <option value="other" <?= ($p['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="civil_status">Civil Status</label>
                    <select id="civil_status" name="civil_status">
                        <option value="">Select</option>
                        <option value="single" <?= ($p['civil_status'] ?? '') === 'single' ? 'selected' : '' ?>>Single</option>
                        <option value="married" <?= ($p['civil_status'] ?? '') === 'married' ? 'selected' : '' ?>>Married</option>
                        <option value="widowed" <?= ($p['civil_status'] ?? '') === 'widowed' ? 'selected' : '' ?>>Widowed</option>
                        <option value="separated" <?= ($p['civil_status'] ?? '') === 'separated' ? 'selected' : '' ?>>Separated</option>
                        <option value="divorced" <?= ($p['civil_status'] ?? '') === 'divorced' ? 'selected' : '' ?>>Divorced</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="place_of_birth">Place of Birth</label>
                    <input type="text" id="place_of_birth" name="place_of_birth" value="<?= esc($p['place_of_birth'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="blood_type">Blood Type</label>
                    <select id="blood_type" name="blood_type">
                        <option value="">Select</option>
                        <option value="A+" <?= ($p['blood_type'] ?? '') === 'A+' ? 'selected' : '' ?>>A+</option>
                        <option value="A-" <?= ($p['blood_type'] ?? '') === 'A-' ? 'selected' : '' ?>>A-</option>
                        <option value="B+" <?= ($p['blood_type'] ?? '') === 'B+' ? 'selected' : '' ?>>B+</option>
                        <option value="B-" <?= ($p['blood_type'] ?? '') === 'B-' ? 'selected' : '' ?>>B-</option>
                        <option value="AB+" <?= ($p['blood_type'] ?? '') === 'AB+' ? 'selected' : '' ?>>AB+</option>
                        <option value="AB-" <?= ($p['blood_type'] ?? '') === 'AB-' ? 'selected' : '' ?>>AB-</option>
                        <option value="O+" <?= ($p['blood_type'] ?? '') === 'O+' ? 'selected' : '' ?>>O+</option>
                        <option value="O-" <?= ($p['blood_type'] ?? '') === 'O-' ? 'selected' : '' ?>>O-</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Contact Information</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="region">Region</label>
                    <select id="region" name="region">
                        <option value="">Select</option>
                        <option value="NCR">NCR</option>
                        <option value="CAR">CAR</option>
                        <option value="Region I">Region I</option>
                        <option value="Region II">Region II</option>
                        <option value="Region III">Region III</option>
                        <option value="Region IV-A">Region IV-A</option>
                        <option value="Region IV-B">Region IV-B</option>
                        <option value="Region V">Region V</option>
                        <option value="Region VI">Region VI</option>
                        <option value="Region VII">Region VII</option>
                        <option value="Region VIII">Region VIII</option>
                        <option value="Region IX">Region IX</option>
                        <option value="Region X">Region X</option>
                        <option value="Region XI">Region XI</option>
                        <option value="Region XII">Region XII</option>
                        <option value="CARAGA">CARAGA</option>
                        <option value="BARMM">BARMM</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="province">Province</label>
                    <select id="province" name="province">
                        <option value="">Select</option>
                        <option value="Metro Manila" <?= ($p['province'] ?? '') === 'Metro Manila' ? 'selected' : '' ?>>Metro Manila</option>
                        <option value="Cebu" <?= ($p['province'] ?? '') === 'Cebu' ? 'selected' : '' ?>>Cebu</option>
                        <option value="Davao del Sur" <?= ($p['province'] ?? '') === 'Davao del Sur' ? 'selected' : '' ?>>Davao del Sur</option>
                        <option value="Other" <?= ($p['province'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="city_municipality">City / Municipality</label>
                    <select id="city_municipality" name="city_municipality">
                        <option value="">Select</option>
                        <option value="Quezon City" <?= ($p['city_municipality'] ?? '') === 'Quezon City' ? 'selected' : '' ?>>Quezon City</option>
                        <option value="Manila" <?= ($p['city_municipality'] ?? '') === 'Manila' ? 'selected' : '' ?>>Manila</option>
                        <option value="Cebu City" <?= ($p['city_municipality'] ?? '') === 'Cebu City' ? 'selected' : '' ?>>Cebu City</option>
                        <option value="Davao City" <?= ($p['city_municipality'] ?? '') === 'Davao City' ? 'selected' : '' ?>>Davao City</option>
                        <option value="Other" <?= ($p['city_municipality'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="barangay">Barangay</label>
                    <select id="barangay" name="barangay">
                        <option value="">Select</option>
                        <option value="Barangay 1" <?= ($p['barangay'] ?? '') === 'Barangay 1' ? 'selected' : '' ?>>Barangay 1</option>
                        <option value="Barangay 2" <?= ($p['barangay'] ?? '') === 'Barangay 2' ? 'selected' : '' ?>>Barangay 2</option>
                        <option value="Barangay 3" <?= ($p['barangay'] ?? '') === 'Barangay 3' ? 'selected' : '' ?>>Barangay 3</option>
                        <option value="Other" <?= ($p['barangay'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="mobile_number">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" value="<?= esc($p['mobile_number'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= esc($p['email'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;" class="outpatient-hidden" id="emergency-contact-section">
            <div class="patient-section-title">Emergency Contact</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="emergency_contact_person">Contact Person</label>
                    <input type="text" id="emergency_contact_person" name="emergency_contact_person" value="<?= esc($p['emergency_contact_person'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="emergency_relationship">Relationship</label>
                    <input type="text" id="emergency_relationship" name="emergency_relationship" value="<?= esc($p['emergency_relationship'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="emergency_contact_number">Contact Number</label>
                    <input type="text" id="emergency_contact_number" name="emergency_contact_number" value="<?= esc($p['emergency_contact_number'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;" class="outpatient-hidden" id="vitals-section">
            <div class="patient-section-title">Vitals</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="blood_pressure">Blood Pressure</label>
                    <input type="text" id="blood_pressure" name="blood_pressure" value="<?= esc($p['blood_pressure'] ?? '') ?>" placeholder="e.g. 120/80">
                </div>
                <div class="form-group-inline">
                    <label for="heart_rate">Heart Rate (bpm)</label>
                    <input type="number" id="heart_rate" name="heart_rate" value="<?= esc($p['heart_rate'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="temperature">Temperature (°C)</label>
                    <input type="number" step="0.1" id="temperature" name="temperature" value="<?= esc($p['temperature'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="height_cm">Height (cm)</label>
                    <input type="number" step="0.1" id="height_cm" name="height_cm" value="<?= esc($p['height_cm'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="weight_kg">Weight (kg)</label>
                    <input type="number" step="0.1" id="weight_kg" name="weight_kg" value="<?= esc($p['weight_kg'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="bmi">BMI</label>
                    <input type="number" step="0.1" id="bmi" name="bmi" value="<?= esc($p['bmi'] ?? '') ?>" readonly>
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;" class="outpatient-hidden" id="insurance-section">
            <div class="patient-section-title">Insurance Information</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="insurance_provider">Insurance Provider</label>
                    <input type="text" id="insurance_provider" name="insurance_provider" value="<?= esc($p['insurance_provider'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="insurance_contact_number">Insurance Contact Number</label>
                    <input type="text" id="insurance_contact_number" name="insurance_contact_number" value="<?= esc($p['insurance_contact_number'] ?? '') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="policy_number">Policy Number</label>
                    <input type="text" id="policy_number" name="policy_number" value="<?= esc($p['policy_number'] ?? '') ?>">
                </div>
            </div>
        </div>

        <div style="margin-bottom:14px;">
            <div class="patient-section-title">Medical Notes</div>
            <div class="form-group-inline">
                <textarea id="medical_notes" name="medical_notes" rows="3"><?= esc($p['medical_notes'] ?? '') ?></textarea>
            </div>
        </div>

        <div id="admission-section" class="admission-section">
            <div class="patient-section-title">Admission Details (Inpatient Only)</div>
            <div class="patient-form-grid">
                <div class="form-group-inline">
                    <label for="admission_date">Admission Date</label>
                    <input type="date" id="admission_date" name="admission_date" value="<?= esc($p['admission_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="form-group-inline">
                    <label for="admission_time">Admission Time</label>
                    <input type="time" id="admission_time" name="admission_time" value="<?= esc($p['admission_time'] ?? date('H:i')) ?>">
                </div>
                <div class="form-group-inline">
                    <label for="room_type">Room Type</label>
                    <select id="room_type" name="room_type">
                        <option value="">Select Room Type</option>
                        <option value="Private" <?= ($p['room_type'] ?? '') === 'Private' ? 'selected' : '' ?>>Private</option>
                        <option value="Semi-Private" <?= ($p['room_type'] ?? '') === 'Semi-Private' ? 'selected' : '' ?>>Semi-Private</option>
                        <option value="Ward" <?= ($p['room_type'] ?? '') === 'Ward' ? 'selected' : '' ?>>Ward</option>
                        <option value="ICU" <?= ($p['room_type'] ?? '') === 'ICU' ? 'selected' : '' ?>>ICU</option>
                        <option value="CCU" <?= ($p['room_type'] ?? '') === 'CCU' ? 'selected' : '' ?>>CCU</option>
                        <option value="Emergency" <?= ($p['room_type'] ?? '') === 'Emergency' ? 'selected' : '' ?>>Emergency</option>
                        <option value="Isolation" <?= ($p['room_type'] ?? '') === 'Isolation' ? 'selected' : '' ?>>Isolation</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="room_number">Room Number</label>
                    <select id="room_number" name="room_number">
                        <option value="">Select Room Number</option>
                        <?php if (!empty($p['room_number'])): ?>
                            <option value="<?= esc($p['room_number']) ?>" selected><?= esc($p['room_number']) ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="bed_number">Bed Number</label>
                    <select id="bed_number" name="bed_number">
                        <option value="">Select Bed Number</option>
                        <?php if (!empty($p['bed_number'])): ?>
                            <option value="<?= esc($p['bed_number']) ?>" selected><?= esc($p['bed_number']) ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-top:16px;display:flex;justify-content:flex-end;gap:8px;">
            <a href="<?= base_url('patients/records') ?>" style="padding:7px 14px;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;font-size:13px;cursor:pointer;text-decoration:none;color:#111827;display:inline-block;">Cancel</a>
            <button type="submit" style="padding:7px 16px;border-radius:999px;border:none;background:#16a34a;color:#ffffff;font-size:13px;font-weight:600;cursor:pointer;">Update Patient Record</button>
        </div>
    </form>
</div>

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
            const emergencySection = document.getElementById('emergency-contact-section');
            const vitalsSection = document.getElementById('vitals-section');
            const insuranceSection = document.getElementById('insurance-section');
            
            if (type === 'inpatient') {
                inpatientBtn.classList.add('active');
                outpatientBtn.classList.remove('active');
                admissionSection.style.display = '';
                // Show all sections for inpatient
                if (emergencySection) emergencySection.style.display = '';
                if (vitalsSection) vitalsSection.style.display = '';
                if (insuranceSection) insuranceSection.style.display = '';
            } else {
                // Outpatient selected
                outpatientBtn.classList.add('active');
                inpatientBtn.classList.remove('active');
                admissionSection.style.display = 'none';
                // Hide sections for outpatient: Emergency Contact, Vitals, Insurance
                if (emergencySection) emergencySection.style.display = 'none';
                if (vitalsSection) vitalsSection.style.display = 'none';
                if (insuranceSection) insuranceSection.style.display = 'none';
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

        if (heightInput && weightInput && bmiInput) {
            heightInput.addEventListener('input', updateBMI);
            weightInput.addEventListener('input', updateBMI);
        }

        // Cascading Region -> Province -> City / Municipality
        const regionSelect = document.getElementById('region');
        const provinceSelect = document.getElementById('province');
        const citySelect = document.getElementById('city_municipality');

        const regionProvinceMap = {
            'Region XII': ['South Cotabato', 'Sultan Kudarat', 'Sarangani', 'Cotabato'],
            'NCR': ['Metro Manila'],
            'CAR': ['Benguet'],
            'Region XI': ['Davao del Sur']
        };

        const provinceCityMap = {
            'Sarangani': ['Alabel', 'Glan', 'Kiamba', 'Maasim', 'Maitum', 'Malapatan', 'Malungon'],
            'South Cotabato': ['Koronadal City', 'General Santos City', 'Polomolok', 'Tupi', 'Surallah', 'Norala', 'Banga'],
            'Sultan Kudarat': ['Isulan', 'Tacurong City', 'Esperanza', 'Lebak'],
            'Cotabato': ['Kidapawan City', 'Midsayap', 'Kabacan'],
            'Metro Manila': ['Quezon City', 'Manila', 'Makati City', 'Pasig City'],
            'Benguet': ['Baguio City'],
            'Davao del Sur': ['Davao City']
        };

        function resetSelect(selectEl, placeholder) {
            if (!selectEl) return;
            selectEl.innerHTML = '';
            const opt = document.createElement('option');
            opt.value = '';
            opt.textContent = placeholder || 'Select';
            selectEl.appendChild(opt);
        }

        function populateSelect(selectEl, items) {
            if (!selectEl) return;
            items.forEach(value => {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = value;
                selectEl.appendChild(opt);
            });
        }

        if (regionSelect && provinceSelect && citySelect) {
            regionSelect.addEventListener('change', function () {
                const region = this.value;
                resetSelect(provinceSelect, 'Select');
                resetSelect(citySelect, 'Select');

                const provinces = regionProvinceMap[region] || [];
                populateSelect(provinceSelect, provinces);
            });

            provinceSelect.addEventListener('change', function () {
                const province = this.value;
                resetSelect(citySelect, 'Select');

                const cities = provinceCityMap[province] || [];
                populateSelect(citySelect, cities);
            });
        }

        // Room and Bed management
        const roomTypeSelect = document.getElementById('room_type');
        const roomNumberSelect = document.getElementById('room_number');
        const bedNumberSelect = document.getElementById('bed_number');
        
        // Define room structure
        const roomStructure = {
            'Private': {
                '101': ['Bed 1'],
                '102': ['Bed 1'],
                '103': ['Bed 1'],
                '104': ['Bed 1'],
                '105': ['Bed 1'],
            },
            'Semi-Private': {
                '201': ['Bed 1', 'Bed 2'],
                '202': ['Bed 1', 'Bed 2'],
                '203': ['Bed 1', 'Bed 2'],
                '204': ['Bed 1', 'Bed 2'],
                '205': ['Bed 1', 'Bed 2'],
            },
            'Ward': {
                '301': ['Bed 1', 'Bed 2', 'Bed 3', 'Bed 4'],
                '302': ['Bed 1', 'Bed 2', 'Bed 3', 'Bed 4'],
                '303': ['Bed 1', 'Bed 2', 'Bed 3', 'Bed 4'],
                '304': ['Bed 1', 'Bed 2', 'Bed 3', 'Bed 4'],
                '305': ['Bed 1', 'Bed 2', 'Bed 3', 'Bed 4'],
            },
            'ICU': {
                '401': ['Bed 1'],
                '402': ['Bed 1'],
                '403': ['Bed 1'],
                '404': ['Bed 1'],
            },
            'CCU': {
                '501': ['Bed 1'],
                '502': ['Bed 1'],
                '503': ['Bed 1'],
            },
            'Emergency': {
                '601': ['Bed 1', 'Bed 2', 'Bed 3'],
                '602': ['Bed 1', 'Bed 2', 'Bed 3'],
                '603': ['Bed 1', 'Bed 2', 'Bed 3'],
            },
            'Isolation': {
                '701': ['Bed 1'],
                '702': ['Bed 1'],
                '703': ['Bed 1'],
            }
        };
        
        if (roomTypeSelect && roomNumberSelect && bedNumberSelect) {
            // Initialize room numbers if room type is already selected
            const currentRoomType = roomTypeSelect.value;
            if (currentRoomType && roomStructure[currentRoomType]) {
                const rooms = Object.keys(roomStructure[currentRoomType]);
                rooms.forEach(room => {
                    const opt = document.createElement('option');
                    opt.value = room;
                    opt.textContent = room;
                    if (room === roomNumberSelect.querySelector('option[selected]')?.value) {
                        opt.selected = true;
                    }
                    roomNumberSelect.appendChild(opt);
                });
                
                // Initialize bed numbers if room number is already selected
                const currentRoomNumber = roomNumberSelect.value;
                if (currentRoomNumber && roomStructure[currentRoomType][currentRoomNumber]) {
                    const beds = roomStructure[currentRoomType][currentRoomNumber];
                    beds.forEach(bed => {
                        const opt = document.createElement('option');
                        opt.value = bed;
                        opt.textContent = bed;
                        if (bed === bedNumberSelect.querySelector('option[selected]')?.value) {
                            opt.selected = true;
                        }
                        bedNumberSelect.appendChild(opt);
                    });
                }
            }
            
            roomTypeSelect.addEventListener('change', function() {
                const roomType = this.value;
                resetSelect(roomNumberSelect, 'Select Room Number');
                resetSelect(bedNumberSelect, 'Select Bed Number');
                
                if (roomType && roomStructure[roomType]) {
                    const rooms = Object.keys(roomStructure[roomType]);
                    populateSelect(roomNumberSelect, rooms);
                }
            });
            
            roomNumberSelect.addEventListener('change', function() {
                const roomType = roomTypeSelect.value;
                const roomNumber = this.value;
                resetSelect(bedNumberSelect, 'Select Bed Number');
                
                if (roomType && roomNumber && roomStructure[roomType] && roomStructure[roomType][roomNumber]) {
                    const beds = roomStructure[roomType][roomNumber];
                    populateSelect(bedNumberSelect, beds);
                }
            });
        }
        
        // Initialize visit type display
        setVisitType('<?= esc($visitType) ?>');
    })();
</script>

<?= $this->endSection() ?>

