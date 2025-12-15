<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">My Schedule</div>
        <div class="page-subtitle">View and manage your work schedule and availability.</div>
    </div>
</div>

<style>
    .sched-card {
        background: #ffffff;
        -webkit-border-radius: 12px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 14px 16px 16px;
        margin-bottom: 14px;
    }
    .sched-card-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .sched-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px 14px;
    }
    .sched-label {
        font-size: 13px;
        margin-bottom: 3px;
        display: block;
    }
    .sched-input,
    .sched-select {
        width: 100%;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 6px 8px;
        font-size: 13px;
    }
    .sched-days-container {
        margin-top: 12px;
    }
    .sched-days {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }
    .sched-day-pill {
        -webkit-border-radius: 999px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 4px 10px;
        font-size: 12px;
        cursor: pointer;
        background: #ffffff;
        display: inline-block;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .sched-day-checkbox {
        display: none;
    }
    .sched-day-pill.active {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
    }
    .sched-form-actions {
        margin-top: 12px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    .sched-btn {
        width: auto;
        -webkit-border-radius: 999px;
        border-radius: 999px;
        padding: 6px 16px;
        font-size: 13px;
        cursor: pointer;
        border: none;
        font-weight: 600;
    }
    .sched-btn-secondary {
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
    }
    .sched-btn-primary {
        background: #16a34a;
        color: #ffffff;
    }
    .sched-btn:hover {
        opacity: 0.9;
    }
    .sched-message {
        padding: 8px 10px;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        font-size: 12px;
        margin-bottom: 10px;
    }
    .sched-message.error {
        background: #ffebe9;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }
    .sched-message.success {
        background: #ecfdf3;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .sched-list {
        margin-top: 16px;
    }
    .sched-item {
        background: #f9fafb;
        -webkit-border-radius: 8px;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        border: 1px solid #e5e7eb;
    }
    .sched-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    .sched-item-title {
        font-weight: 600;
        font-size: 14px;
    }
    .sched-item-badge {
        display: inline-block;
        padding: 2px 8px;
        -webkit-border-radius: 999px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
    }
    .sched-item-badge.admin {
        background: #dbeafe;
        color: #1e40af;
    }
    .sched-item-badge.doctor {
        background: #dcfce7;
        color: #166534;
    }
    .sched-item-details {
        font-size: 12px;
        color: #6b7280;
    }
    .sched-item-time {
        font-weight: normal;
        color: #6b7280;
        margin-left: 8px;
    }
    .sched-item-days {
        margin-top: 4px;
    }
    .sched-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }
    .sched-empty-icon {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
    .sched-empty-title {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .sched-empty-text {
        font-size: 12px;
    }
</style>

<?php if (session()->getFlashdata('error')): ?>
    <div class="sched-message error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="sched-message success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="sched-card">
    <div class="sched-card-title">Create New Schedule</div>
    <form action="/WebSys_HMS_G3/doctor/schedule" method="post" id="doctor-schedule-form">
        <?= csrf_field() ?>
        <div class="sched-grid">
            <div>
                <label for="shift_name" class="sched-label">Shift Name</label>
                <input type="text" id="shift_name" name="shift_name" class="sched-input" placeholder="e.g. Morning Shift" title="Enter a name for this shift">
            </div>
            <div>
                <label for="start_time" class="sched-label">Start Time</label>
                <input type="time" id="start_time" name="start_time" class="sched-input" title="Select the start time for this schedule">
            </div>
            <div>
                <label for="end_time" class="sched-label">End Time</label>
                <input type="time" id="end_time" name="end_time" class="sched-input" title="Select the end time for this schedule">
            </div>
            <div>
                <label for="valid_from" class="sched-label">Valid From</label>
                <input type="date" id="valid_from" name="valid_from" class="sched-input" value="<?= date('Y-m-d') ?>" title="Select the start date for this schedule">
            </div>
            <div>
                <label for="valid_to" class="sched-label">Valid To</label>
                <input type="date" id="valid_to" name="valid_to" class="sched-input" value="<?= date('Y-m-d', strtotime('+1 year')) ?>" title="Select the end date for this schedule">
            </div>
        </div>
        <div class="sched-days-container">
            <div class="sched-label">Days of Week</div>
            <div class="sched-days" role="group" aria-label="Select days of the week">
                <?php 
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $day): 
                    $dayValue = strtolower($day);
                    $dayId = 'day_' . $dayValue;
                ?>
                    <label for="<?= $dayId ?>" class="sched-day-pill">
                        <input type="checkbox" id="<?= $dayId ?>" name="days[]" value="<?= $dayValue ?>" class="sched-day-checkbox" aria-label="Select <?= $day ?>">
                        <span><?= $day ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="sched-form-actions">
            <button type="reset" id="clear-schedule" class="sched-btn sched-btn-secondary">Clear</button>
            <button type="submit" id="save-schedule" class="sched-btn sched-btn-primary">Save Schedule</button>
        </div>
    </form>
</div>

<?php if (!empty($schedules)): ?>
<div class="sched-card">
    <div style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">My Schedules</div>
    <div class="sched-list">
        <?php foreach ($schedules as $schedule): ?>
            <div class="sched-item">
                <div class="sched-item-header">
                    <div class="sched-item-title">
                        <?= esc($schedule['shift_name'] ?: 'Schedule #' . $schedule['id']) ?>
                        <?php if (!empty($schedule['start_time']) && !empty($schedule['end_time'])): ?>
                            <span class="sched-item-time">
                                <?= date('h:i A', strtotime($schedule['start_time'])) ?> - <?= date('h:i A', strtotime($schedule['end_time'])) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <span class="sched-item-badge <?= ($schedule['created_by'] ?? 'admin') === 'doctor' ? 'doctor' : 'admin' ?>">
                        <?= ($schedule['created_by'] ?? 'admin') === 'doctor' ? 'Created by Me' : 'Created by Admin' ?>
                    </span>
                </div>
                <div class="sched-item-details">
                    <div>Valid: <?= $schedule['valid_from'] ? date('M d, Y', strtotime($schedule['valid_from'])) : 'N/A' ?> - <?= $schedule['valid_to'] ? date('M d, Y', strtotime($schedule['valid_to'])) : 'N/A' ?></div>
                    <?php if (!empty($schedule['days'])): ?>
                        <div class="sched-item-days">
                            Days: <?= esc(implode(', ', array_map('ucfirst', $schedule['days']))) ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="sched-card">
    <div class="sched-empty-state">
        <div class="sched-empty-icon" aria-hidden="true">📅</div>
        <div class="sched-empty-title">No schedules found</div>
        <div class="sched-empty-text">Create a schedule above to get started.</div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Day pill toggle
    const dayPills = document.querySelectorAll('.sched-day-pill');
    dayPills.forEach(pill => {
        const checkbox = pill.querySelector('input[type="checkbox"]');
        if (!checkbox) return;
        
        // Initialize active state
        if (checkbox.checked) {
            pill.classList.add('active');
        }
        
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            checkbox.checked = !checkbox.checked;
            if (checkbox.checked) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
            console.log('Day toggled:', checkbox.value, checkbox.checked);
        });
        
        // Also handle checkbox change directly
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
        });
    });

    // Form validation before submission
    const form = document.getElementById('doctor-schedule-form');
    const saveButton = document.getElementById('save-schedule');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('=== FORM SUBMISSION STARTED ===');
            
            const checkedDays = form.querySelectorAll('input[name="days[]"]:checked');
            console.log('Checked days count:', checkedDays.length);
            console.log('Checked days values:', Array.from(checkedDays).map(cb => cb.value));
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            // Check CSRF token
            const csrfInput = form.querySelector('input[name="csrf_test_name"]');
            if (csrfInput) {
                console.log('CSRF token found:', csrfInput.value.substring(0, 10) + '...');
            } else {
                console.error('CSRF token NOT found!');
            }
            
            if (checkedDays.length === 0) {
                e.preventDefault();
                e.stopPropagation();
                alert('Please select at least one day of availability.');
                console.log('Form submission prevented: No days selected');
                return false;
            }
            
            // Log all form data
            const formData = new FormData(form);
            console.log('Form data being submitted:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Disable button to prevent double submission
            if (saveButton) {
                saveButton.disabled = true;
                saveButton.textContent = 'Saving...';
            }
            
            console.log('Form will submit normally - allowing default behavior');
            // CRITICAL: Don't prevent default - let form submit normally
            return true;
        });
    } else {
        console.error('Form not found: doctor-schedule-form');
    }
});
</script>

<?= $this->endSection() ?>

