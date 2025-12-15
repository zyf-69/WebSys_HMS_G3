<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Appointments</div>
        <div class="page-subtitle">Create patient appointments and review them on the schedule calendar.</div>
    </div>
</div>

<style>
    .appt-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 14px 16px 16px;
        margin-bottom: 14px;
    }
    .appt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px 14px;
    }
    .appt-label {
        font-size: 13px;
        margin-bottom: 3px;
        font-weight: 500;
    }
    .appt-input,
    .appt-select {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 6px 8px;
        font-size: 13px;
    }
    .appt-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 13px;
    }
    .appt-toggle-group {
        display: inline-flex;
        border-radius: 999px;
        background: #e5e7eb;
        padding: 2px;
    }
    .appt-toggle-btn {
        border: none;
        background: transparent;
        padding: 5px 12px;
        font-size: 12px;
        border-radius: 999px;
        cursor: pointer;
    }
    .appt-toggle-btn.active {
        background: #111827;
        color: #ffffff;
    }
    .appt-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 4px;
        font-size: 12px;
    }
    .appt-calendar-day-header {
        font-weight: 600;
        color: #6b7280;
        text-align: center;
        padding-bottom: 4px;
    }
    .appt-calendar-cell {
        min-height: 80px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 4px 5px;
        display: flex;
        flex-direction: column;
    }
    .appt-calendar-date {
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 3px;
    }
    .appt-badge {
        border-radius: 999px;
        padding: 1px 5px;
        font-size: 10px;
        background: #0ea5e9;
        color: #ffffff;
        display: inline-block;
        margin-bottom: 2px;
    }
    .appt-entry {
        font-size: 10px;
        margin-bottom: 2px;
        color: #4b5563;
    }
    .appt-message {
        padding: 8px 10px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 10px;
    }
    .appt-message.error {
        background: #ffebe9;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .appt-message.success {
        background: #ecfdf3;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
</style>

<?php 
$patients = $patients ?? []; 
$doctors = $doctors ?? [];
$schedules = $schedules ?? []; 
$appointments = $appointments ?? []; 
?>

<div class="appt-card">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="appt-message error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="appt-message success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <form action="/WebSys_HMS_G3/admin/appointments" method="post" id="appointment-form">
        <?= csrf_field() ?>
        <div class="appt-grid">
            <div>
                <div class="appt-label">Patient</div>
                <select name="patient_id" id="patient_id" class="appt-select" required>
                    <option value="">Select patient</option>
                    <?php foreach ($patients as $p): ?>
                        <?php 
                        $name = trim(($p['last_name'] ?? '') . ', ' . ($p['first_name'] ?? '') . ' ' . ($p['middle_name'] ?? ''));
                        $patientType = $p['patient_type'] ?? 'Outpatient';
                        ?>
                        <option value="<?= esc($p['id']) ?>" data-doctor-id="<?= esc($p['doctor_id'] ?? '') ?>" data-doctor-name="<?= esc($p['doctor_name'] ?? '') ?>">
                            <?= esc($name) ?> (<?= esc($patientType) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <div class="appt-label">Doctor Available</div>
                <select id="doctor_id_display" class="appt-select" disabled style="background-color: #f3f4f6; cursor: not-allowed;">
                    <option value="">Select patient first</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= esc($doctor['id']) ?>">
                            <?= esc($doctor['full_name']) ?>
                            <?php if (!empty($doctor['specialization'])): ?>
                                • <?= esc($doctor['specialization']) ?>
                            <?php endif; ?>
                            <?php if (!empty($doctor['license_number'])): ?>
                                (<?= esc($doctor['license_number']) ?>)
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" name="doctor_id" id="doctor_id" required>
                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">Auto-populated from patient's assigned doctor</div>
            </div>
            <div id="schedule-selection" style="display: none;">
                <div class="appt-label">Schedule</div>
                <select name="doctor_schedule_id" id="doctor_schedule_id" class="appt-select">
                    <option value="">Select schedule (optional)</option>
                </select>
                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">If no schedule, a default will be created</div>
                <div id="schedule-error" style="color: red; font-size: 11px; margin-top: 2px; display: none;">Please select a schedule</div>
            </div>
            <div id="appointment-time-selection" style="display: none;">
                <div class="appt-label">Appointment Time</div>
                <input type="time" name="appointment_time" id="appointment_time" class="appt-input">
                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                    <span id="schedule-time-range">Select time within schedule (optional)</span>
                </div>
            </div>
            <div>
                <div class="appt-label">Schedule Type</div>
                <select name="schedule_type" class="appt-select">
                    <option value="">Select type</option>
                    <option value="consultation">Consultation</option>
                    <option value="follow_up">Follow-up</option>
                    <option value="procedure">Procedure</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
            <div>
                <div class="appt-label">Appointment Date</div>
                <input type="date" name="appointment_date" class="appt-input" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>
        <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:8px;">
            <button type="reset" class="appt-input" style="width:auto;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;cursor:pointer;">Clear</button>
            <button type="submit" class="appt-input" style="width:auto;border-radius:999px;border:none;background:#0ea5e9;color:#ffffff;font-weight:600;cursor:pointer;">Save Appointment</button>
        </div>
    </form>
</div>

<div class="appt-card">
    <div class="appt-toolbar">
        <div>Appointments Calendar (next 12 months)</div>
        <div class="appt-toggle-group">
            <button type="button" class="appt-toggle-btn active" data-view="week">Week</button>
            <button type="button" class="appt-toggle-btn" data-view="month">Month</button>
        </div>
    </div>
    <div class="appt-calendar" id="appt-calendar"></div>
</div>

<script>
// Global data from PHP (available immediately)
const schedulesData = <?= json_encode($schedules ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const appointmentsData = <?= json_encode($appointments ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

console.log('Appointments page loaded');
console.log('Schedules data:', schedulesData);
console.log('Appointments data:', appointmentsData);

// Wait for page to load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate doctor when patient is selected
    const patientSelect = document.getElementById('patient_id');
    const doctorSelectDisplay = document.getElementById('doctor_id_display');
    const doctorSelectHidden = document.getElementById('doctor_id');
    const scheduleSelect = document.getElementById('doctor_schedule_id');
    const scheduleSelection = document.getElementById('schedule-selection');
    const appointmentTimeSelection = document.getElementById('appointment-time-selection');
    const appointmentTimeInput = document.getElementById('appointment_time');
    const scheduleTimeRange = document.getElementById('schedule-time-range');
    const scheduleError = document.getElementById('schedule-error');
    
    if (patientSelect && doctorSelectDisplay && doctorSelectHidden) {
        patientSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const doctorId = selectedOption.getAttribute('data-doctor-id');
            const doctorName = selectedOption.getAttribute('data-doctor-name');
            
            if (doctorId && doctorId !== '') {
                doctorSelectDisplay.value = doctorId;
                doctorSelectHidden.value = doctorId;
                console.log('Auto-populated doctor:', doctorName, 'ID:', doctorId);
                // Trigger schedule update
                if (scheduleSelect && scheduleSelection) {
                    updateScheduleDropdown(parseInt(doctorId));
                }
            } else {
                doctorSelectDisplay.value = '';
                doctorSelectHidden.value = '';
                if (scheduleSelection) scheduleSelection.style.display = 'none';
                if (appointmentTimeSelection) appointmentTimeSelection.style.display = 'none';
                alert('This patient does not have an assigned doctor. Please assign a doctor in Patient Registration first.');
            }
        });
    }

    if (!scheduleSelect || !scheduleSelection) {
        console.error('Required elements not found!', {
            scheduleSelect: !!scheduleSelect,
            scheduleSelection: !!scheduleSelection
        });
    }

    function updateScheduleDropdown(doctorId) {
        console.log('Updating schedule dropdown for doctor:', doctorId);
        scheduleSelect.innerHTML = '<option value="">Select schedule</option>';
        appointmentTimeSelection.style.display = 'none';
        scheduleSelection.style.display = 'none';
        if (scheduleError) scheduleError.style.display = 'none';
        
        if (!doctorId) {
            console.log('No doctor selected');
            return;
        }
        
        const doctorSchedules = schedulesData.filter(s => parseInt(s.doctor_id) === parseInt(doctorId));
        console.log('Doctor schedules found:', doctorSchedules);
        
        if (doctorSchedules.length > 0) {
            scheduleSelection.style.display = 'block';
            doctorSchedules.forEach(schedule => {
                const option = document.createElement('option');
                option.value = schedule.id;
                
                let label = '';
                if (schedule.shift_name) label += schedule.shift_name;
                if (schedule.start_time && schedule.end_time) {
                    const start = schedule.start_time.substring(0, 5);
                    const end = schedule.end_time.substring(0, 5);
                    label += (label ? ' • ' : '') + start + ' - ' + end;
                }
                
                option.textContent = label || 'Schedule #' + schedule.id;
                scheduleSelect.appendChild(option);
            });
            console.log('Schedule dropdown populated with', doctorSchedules.length, 'schedules');
        } else {
            console.warn('No schedules found for doctor:', doctorId);
            // Don't show alert - allow creating appointment without schedule
            // A default schedule will be created automatically
            scheduleSelection.style.display = 'block';
            console.log('No schedules found, but allowing appointment creation');
        }
    }

    function updateAppointmentTime() {
        const scheduleId = scheduleSelect.value;
        console.log('Updating appointment time for schedule:', scheduleId);
        
        if (!scheduleId) {
            appointmentTimeSelection.style.display = 'none';
            return;
        }
        
        const schedule = schedulesData.find(s => parseInt(s.id) === parseInt(scheduleId));
        console.log('Found schedule:', schedule);
        
        if (schedule && schedule.start_time && schedule.end_time) {
            const start = schedule.start_time.substring(0, 5);
            const end = schedule.end_time.substring(0, 5);
            
            appointmentTimeInput.min = start;
            appointmentTimeInput.max = end;
            appointmentTimeInput.value = start;
            
            if (scheduleTimeRange) {
                scheduleTimeRange.textContent = 'Available: ' + start + ' - ' + end;
            }
            
            appointmentTimeSelection.style.display = 'block';
            if (scheduleError) scheduleError.style.display = 'none';
            console.log('Appointment time field shown');
        }
    }

    // Add event listeners
    // Doctor is auto-populated from patient, so schedule updates when patient is selected
    // (handled in patientSelect event listener above)

    if (scheduleSelect) {
        scheduleSelect.addEventListener('change', function() {
            console.log('Schedule changed to:', this.value);
            updateAppointmentTime();
        });
    }

    // Form validation and submission
    const form = document.getElementById('appointment-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            const formData = new FormData(form);
            console.log('Form data:', {
                patient_id: formData.get('patient_id'),
                doctor_id: formData.get('doctor_id'),
                doctor_schedule_id: formData.get('doctor_schedule_id'),
                appointment_date: formData.get('appointment_date'),
                appointment_time: formData.get('appointment_time'),
                schedule_type: formData.get('schedule_type')
            });
            
            // Schedule is now optional - if not selected, a default will be created
            console.log('Form validation passed, submitting...');
        });
    }
});

// Calendar - Initialize after DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('appt-calendar');
    const toggleButtons = document.querySelectorAll('.appt-toggle-btn');
    const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }

    function buildCalendar(view) {
        if (!calendarEl) return;
        
        console.log('Building calendar with view:', view);
        console.log('Appointments to display:', appointmentsData);
        
        calendarEl.innerHTML = '';

        const today = new Date();
        let start = new Date(today);
        let end = new Date(today);

        if (view === 'week') {
            const day = start.getDay();
            start.setDate(start.getDate() - day);
            end = new Date(start);
            end.setDate(start.getDate() + 6);
        } else {
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        }

        const cells = [];
        const cursor = new Date(start);
        while (cursor <= end) {
            cells.push(new Date(cursor));
            cursor.setDate(cursor.getDate() + 1);
        }

        function apptsForDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const isoDate = `${year}-${month}-${day}`;
            
            const matching = appointmentsData.filter(a => {
                if (!a || !a.appointment_date) return false;
                const apptDate = String(a.appointment_date).split(' ')[0].trim();
                return apptDate === isoDate;
            });
            
            console.log(`Appointments for ${isoDate}:`, matching);
            return matching;
        }

        const grid = document.createElement('div');
        grid.className = 'appt-calendar-grid';

        // Add day headers
        dayNames.forEach(name => {
            const h = document.createElement('div');
            h.className = 'appt-calendar-day-header';
            h.textContent = name.substr(0, 3);
            grid.appendChild(h);
        });

        // Add empty cells for days before the first day
        let leadingEmpty = cells[0].getDay();
        for (let i = 0; i < leadingEmpty; i++) {
            const empty = document.createElement('div');
            empty.className = 'appt-calendar-cell';
            grid.appendChild(empty);
        }

        // Add date cells with appointments
        cells.forEach(date => {
            const cell = document.createElement('div');
            cell.className = 'appt-calendar-cell';

            const dateLabel = document.createElement('div');
            dateLabel.className = 'appt-calendar-date';
            dateLabel.textContent = date.getDate();
            cell.appendChild(dateLabel);

            const dayAppts = apptsForDate(date);
            if (dayAppts.length > 0) {
                dayAppts.forEach(a => {
                    // Status badge
                    const statusBadge = document.createElement('div');
                    statusBadge.className = 'appt-badge';
                    const status = (a.status || 'pending').toLowerCase();
                    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    statusBadge.style.background = status === 'pending' ? '#f59e0b' : 
                                                   status === 'completed' ? '#10b981' : 
                                                   status === 'cancelled' ? '#ef4444' : '#0ea5e9';
                    statusBadge.style.marginBottom = '3px';
                    cell.appendChild(statusBadge);

                    // Time display
                    if (a.start_time) {
                        const timeEntry = document.createElement('div');
                        timeEntry.className = 'appt-entry';
                        const timeStr = String(a.start_time).substring(0, 5);
                        const endTimeStr = a.end_time ? String(a.end_time).substring(0, 5) : '';
                        timeEntry.textContent = timeStr + (endTimeStr ? ' - ' + endTimeStr : '');
                        timeEntry.style.fontWeight = '600';
                        timeEntry.style.color = '#111827';
                        timeEntry.style.fontSize = '10px';
                        timeEntry.style.marginBottom = '2px';
                        cell.appendChild(timeEntry);
                    }

                    // Patient and doctor info
                    const entry = document.createElement('div');
                    entry.className = 'appt-entry';
                    const patientName = [
                        a.last_name || '', 
                        a.first_name || '', 
                        a.middle_name || ''
                    ].filter(Boolean).join(', ');
                    const doctorName = a.doctor_name || 'N/A';
                    entry.textContent = (patientName || 'Unknown') + ' • ' + doctorName;
                    entry.style.fontSize = '10px';
                    entry.style.color = '#4b5563';
                    entry.style.marginBottom = '2px';
                    cell.appendChild(entry);
                });
            }

            grid.appendChild(cell);
        });

        calendarEl.appendChild(grid);
        console.log('Calendar built successfully');
    }

    // Add toggle button listeners
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const view = this.getAttribute('data-view');
            toggleButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            buildCalendar(view);
        });
    });

    // Initial calendar build
    buildCalendar('week');
});
</script>

<?= $this->endSection() ?>
