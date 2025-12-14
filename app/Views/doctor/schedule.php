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
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 14px 16px 16px;
        margin-bottom: 14px;
    }
    .sched-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 10px 14px;
    }
    .sched-label {
        font-size: 13px;
        margin-bottom: 3px;
    }
    .sched-input,
    .sched-select {
        width: 100%;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        padding: 6px 8px;
        font-size: 13px;
    }
    .sched-days {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }
    .sched-day-pill {
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 4px 10px;
        font-size: 12px;
        cursor: pointer;
        background: #ffffff;
    }
    .sched-day-pill.active {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
    }
    .sched-message {
        padding: 8px 10px;
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
    <div style="font-size: 14px; font-weight: 600; margin-bottom: 12px;">Create New Schedule</div>
    <form action="<?= base_url('doctor/schedule') ?>" method="post" id="doctor-schedule-form">
        <?= csrf_field() ?>
        <div class="sched-grid">
            <div>
                <div class="sched-label">Shift Name</div>
                <input type="text" name="shift_name" class="sched-input" placeholder="e.g. Morning Shift">
            </div>
            <div>
                <div class="sched-label">Start Time</div>
                <input type="time" name="start_time" class="sched-input">
            </div>
            <div>
                <div class="sched-label">End Time</div>
                <input type="time" name="end_time" class="sched-input">
            </div>
            <div>
                <div class="sched-label">Valid From</div>
                <input type="date" name="valid_from" class="sched-input" value="<?= date('Y-m-d') ?>">
            </div>
            <div>
                <div class="sched-label">Valid To</div>
                <input type="date" name="valid_to" class="sched-input" value="<?= date('Y-m-d', strtotime('+1 year')) ?>">
            </div>
        </div>
        <div style="margin-top: 12px;">
            <div class="sched-label">Days of Week</div>
            <div class="sched-days">
                <?php 
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $day): 
                ?>
                    <label class="sched-day-pill">
                        <input type="checkbox" name="days[]" value="<?= strtolower($day) ?>" style="display: none;">
                        <?= $day ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <div style="margin-top: 12px; display: flex; justify-content: flex-end; gap: 8px;">
            <button type="reset" class="sched-input" style="width: auto; border-radius: 999px; border: 1px solid #d1d5db; background: #ffffff; cursor: pointer;">Clear</button>
            <button type="submit" class="sched-input" style="width: auto; border-radius: 999px; border: none; background: #16a34a; color: #ffffff; font-weight: 600; cursor: pointer;">Save Schedule</button>
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
                            <span style="font-weight: normal; color: #6b7280; margin-left: 8px;">
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
                        <div style="margin-top: 4px;">
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
    <div style="text-align: center; padding: 40px 20px; color: #6b7280;">
        <div style="font-size: 48px; margin-bottom: 12px; opacity: 0.5;">📅</div>
        <div style="font-size: 14px; font-weight: 500; margin-bottom: 4px;">No schedules found</div>
        <div style="font-size: 12px;">Create a schedule above to get started.</div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Day pill toggle
    const dayPills = document.querySelectorAll('.sched-day-pill');
    dayPills.forEach(pill => {
        const checkbox = pill.querySelector('input[type="checkbox"]');
        pill.addEventListener('click', function() {
            checkbox.checked = !checkbox.checked;
            if (checkbox.checked) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
        });
        
        // Initialize active state
        if (checkbox.checked) {
            pill.classList.add('active');
        }
    });
});
</script>

<?= $this->endSection() ?>

