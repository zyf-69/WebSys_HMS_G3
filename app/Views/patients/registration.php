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
        .outpatient-hidden {
            /* Will be controlled by JavaScript based on visit type */
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
                    <select id="civil_status" name="civil_status">
                        <option value="">Select</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
                        <option value="divorced">Divorced</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="place_of_birth">Place of Birth</label>
                    <input type="text" id="place_of_birth" name="place_of_birth">
                </div>
                <div class="form-group-inline">
                    <label for="blood_type">Blood Type</label>
                    <select id="blood_type" name="blood_type">
                        <option value="">Select</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
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
                        <option value="Metro Manila">Metro Manila</option>
                        <option value="Cebu">Cebu</option>
                        <option value="Davao del Sur">Davao del Sur</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="city_municipality">City / Municipality</label>
                    <select id="city_municipality" name="city_municipality">
                        <option value="">Select</option>
                        <option value="Quezon City">Quezon City</option>
                        <option value="Manila">Manila</option>
                        <option value="Cebu City">Cebu City</option>
                        <option value="Davao City">Davao City</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="barangay">Barangay</label>
                    <select id="barangay" name="barangay">
                        <option value="">Select</option>
                        <option value="Barangay 1">Barangay 1</option>
                        <option value="Barangay 2">Barangay 2</option>
                        <option value="Barangay 3">Barangay 3</option>
                        <option value="Other">Other</option>
                    </select>
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

        <div style="margin-bottom:14px;" class="outpatient-hidden" id="emergency-contact-section">
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

        <div style="margin-bottom:14px;" class="outpatient-hidden" id="vitals-section">
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

        <div style="margin-bottom:14px;" class="outpatient-hidden" id="insurance-section">
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
                    <input type="date" id="admission_date" name="admission_date" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group-inline">
                    <label for="admission_time">Admission Time</label>
                    <input type="time" id="admission_time" name="admission_time">
                </div>
                <div class="form-group-inline">
                    <label for="room_type">Room Type</label>
                    <select id="room_type" name="room_type">
                        <option value="">Select Room Type</option>
                        <option value="Private">Private</option>
                        <option value="Semi-Private">Semi-Private</option>
                        <option value="Ward">Ward</option>
                        <option value="ICU">ICU</option>
                        <option value="CCU">CCU</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Isolation">Isolation</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="room_number">Room Number</label>
                    <select id="room_number" name="room_number">
                        <option value="">Select Room Number</option>
                    </select>
                </div>
                <div class="form-group-inline">
                    <label for="bed_number">Bed Number</label>
                    <select id="bed_number" name="bed_number">
                        <option value="">Select Bed Number</option>
                    </select>
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
                
                // Update admission date and time to current when switching to inpatient
                const admissionDateInput = document.getElementById('admission_date');
                const admissionTimeInput = document.getElementById('admission_time');
                if (admissionDateInput) {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    admissionDateInput.value = `${year}-${month}-${day}`;
                }
                if (admissionTimeInput) {
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    admissionTimeInput.value = `${hours}:${minutes}`;
                }
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

        // Set current date and time for admission
        const admissionDateInput = document.getElementById('admission_date');
        const admissionTimeInput = document.getElementById('admission_time');
        
        if (admissionDateInput) {
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            admissionDateInput.value = `${year}-${month}-${day}`;
        }
        
        if (admissionTimeInput) {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            admissionTimeInput.value = `${hours}:${minutes}`;
        }
        
        // defaults - set to inpatient and show all sections
        setVisitType('inpatient');
        setRecordFilter('all');
        
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
        
        if (roomTypeSelect && roomNumberSelect && bedNumberSelect) {
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
    })();
</script>

<?= $this->endSection() ?>
