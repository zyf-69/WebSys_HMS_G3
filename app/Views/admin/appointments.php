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
        font-size: 11px;
        margin-bottom: 2px;
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

<?php $patients = $patients ?? []; $schedules = $schedules ?? []; $appointments = $appointments ?? []; ?>

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

    <form action="<?= base_url('admin/appointments') ?>" method="post">
        <?= csrf_field() ?>
        <div class="appt-grid">
            <div>
                <div class="appt-label">Patient</div>
                <select name="patient_id" class="appt-select" required>
                    <option value="">Select patient</option>
                    <?php foreach ($patients as $p): ?>
                        <?php $name = trim(($p['last_name'] ?? '') . ', ' . ($p['first_name'] ?? '') . ' ' . ($p['middle_name'] ?? '')); ?>
                        <option value="<?= esc($p['id']) ?>"><?= esc($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <div class="appt-label">Doctor Schedule</div>
                <select name="doctor_schedule_id" class="appt-select" required>
                    <option value="">Select doctor schedule</option>
                    <?php foreach ($schedules as $s): ?>
                        <?php
                        $timeRange = ($s['start_time'] && $s['end_time'])
                            ? substr($s['start_time'], 0, 5) . ' - ' . substr($s['end_time'], 0, 5)
                            : '';
                        $validRange = ($s['valid_from'] && $s['valid_to'])
                            ? $s['valid_from'] . ' to ' . $s['valid_to']
                            : '';
                        ?>
                        <option value="<?= esc($s['id']) ?>">
                            <?= esc($s['full_name']) ?><?= $s['shift_name'] ? ' • ' . esc($s['shift_name']) : '' ?><?= $timeRange ? ' • ' . esc($timeRange) : '' ?><?= $validRange ? ' • ' . esc($validRange) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
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
    (function() {
        const appointments = <?= json_encode($appointments, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const calendarEl = document.getElementById('appt-calendar');
        const toggleButtons = document.querySelectorAll('.appt-toggle-btn');
        const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        function buildCalendar(view) {
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
                const isoDate = date.toISOString().slice(0, 10);
                return appointments.filter(a => a.appointment_date === isoDate);
            }

            const grid = document.createElement('div');
            grid.className = 'appt-calendar-grid';

            dayNames.forEach(name => {
                const h = document.createElement('div');
                h.className = 'appt-calendar-day-header';
                h.textContent = name.substr(0, 3);
                grid.appendChild(h);
            });

            let leadingEmpty = cells[0].getDay();
            for (let i = 0; i < leadingEmpty; i++) {
                const empty = document.createElement('div');
                empty.className = 'appt-calendar-cell';
                grid.appendChild(empty);
            }

            cells.forEach(date => {
                const cell = document.createElement('div');
                cell.className = 'appt-calendar-cell';

                const dateLabel = document.createElement('div');
                dateLabel.className = 'appt-calendar-date';
                dateLabel.textContent = date.getDate();
                cell.appendChild(dateLabel);

                const dayAppts = apptsForDate(date);
                dayAppts.forEach(a => {
                    const badge = document.createElement('div');
                    badge.className = 'appt-badge';
                    badge.textContent = (a.schedule_type || 'Appointment').replace('_', ' ');
                    cell.appendChild(badge);

                    const entry = document.createElement('div');
                    entry.className = 'appt-entry';
                    const patientName = [a.last_name, a.first_name, a.middle_name].filter(Boolean).join(', ');
                    entry.textContent = patientName + ' • ' + (a.doctor_name || '');
                    cell.appendChild(entry);
                });

                grid.appendChild(cell);
            });

            calendarEl.appendChild(grid);
        }

        toggleButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const view = this.getAttribute('data-view');
                toggleButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                buildCalendar(view);
            });
        });

        buildCalendar('week');
    })();
</script>

<?= $this->endSection() ?>
