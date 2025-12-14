<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Doctor Scheduling</div>
        <div class="page-subtitle">Assign work shifts and availability days for doctors, and review their schedules on the calendar.</div>
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
    .sched-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        font-size: 13px;
    }
    .sched-toggle-group {
        display: inline-flex;
        border-radius: 999px;
        background: #e5e7eb;
        padding: 2px;
    }
    .sched-toggle-btn {
        border: none;
        background: transparent;
        padding: 5px 12px;
        font-size: 12px;
        border-radius: 999px;
        cursor: pointer;
    }
    .sched-toggle-btn.active {
        background: #111827;
        color: #ffffff;
    }
    .sched-calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 4px;
        font-size: 12px;
    }
    .sched-calendar-day-header {
        font-weight: 600;
        color: #6b7280;
        text-align: center;
        padding-bottom: 4px;
    }
    .sched-calendar-cell {
        min-height: 80px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 4px 5px;
        display: flex;
        flex-direction: column;
    }
    .sched-calendar-date {
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 3px;
    }
    .sched-badge {
        border-radius: 999px;
        padding: 1px 5px;
        font-size: 10px;
        background: #16a34a;
        color: #ffffff;
        display: inline-block;
        margin-bottom: 2px;
    }
    .sched-entry {
        font-size: 11px;
        margin-bottom: 2px;
    }
</style>

<?php $doctors = $doctors ?? []; $schedules = $schedules ?? []; ?>

<div class="sched-card">
    <form action="<?= base_url('admin/scheduling') ?>" method="post" id="doctor-scheduling-form">
        <?= csrf_field() ?>
        <div class="sched-grid">
            <div>
                <div class="sched-label">Doctor</div>
                <select name="doctor_id" class="sched-select" required>
                    <option value="">Select doctor</option>
                    <?php foreach ($doctors as $doc): ?>
                        <option value="<?= esc($doc['id']) ?>">
                            <?= esc($doc['full_name']) ?><?= $doc['specialization'] ? ' - ' . esc($doc['specialization']) : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
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
                <div class="sched-label">Valid To (max 1 year)</div>
                <input type="date" name="valid_to" class="sched-input" value="<?= date('Y-m-d', strtotime('+1 year')) ?>">
            </div>
        </div>
        <div style="margin-top:10px;">
            <div class="sched-label">Days of Availability</div>
            <div class="sched-days" id="sched-days">
                <?php
                $days = ['monday' => 'Mon','tuesday' => 'Tue','wednesday' => 'Wed','thursday' => 'Thu','friday' => 'Fri','saturday' => 'Sat','sunday' => 'Sun'];
                foreach ($days as $key => $label): ?>
                    <button type="button" class="sched-day-pill" data-day="<?= $key ?>"><?= $label ?></button>
                <?php endforeach; ?>
            </div>
            <!-- Hidden inputs generated by JS for selected days -->
        </div>
        <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:8px;">
            <button type="reset" class="sched-input" style="width:auto;border-radius:999px;border:1px solid #d1d5db;background:#ffffff;cursor:pointer;">Clear</button>
            <button type="submit" class="sched-input" style="width:auto;border-radius:999px;border:none;background:#16a34a;color:#ffffff;font-weight:600;cursor:pointer;">Save Schedule</button>
        </div>
    </form>
</div>

<div class="sched-card">
    <div class="sched-toolbar">
        <div>Doctor Schedule Calendar (next 12 months)</div>
        <div class="sched-toggle-group">
            <button type="button" class="sched-toggle-btn active" data-view="week">Week</button>
            <button type="button" class="sched-toggle-btn" data-view="month">Month</button>
        </div>
    </div>
    <div class="sched-calendar" id="sched-calendar"></div>
</div>

<script>
    (function() {
        const dayPills = document.querySelectorAll('.sched-day-pill');
        const form = document.getElementById('doctor-scheduling-form');

        function syncDayInputs() {
            // Remove existing day hidden inputs
            form.querySelectorAll('input[name="days[]"]').forEach(el => el.remove());
            // Add for each active pill
            dayPills.forEach(pill => {
                if (pill.classList.contains('active')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'days[]';
                    input.value = pill.getAttribute('data-day');
                    form.appendChild(input);
                }
            });
        }

        dayPills.forEach(pill => {
            pill.addEventListener('click', function () {
                this.classList.toggle('active');
                syncDayInputs();
            });
        });

        // Calendar logic (simple projected schedule over next year)
        const schedules = <?= json_encode($schedules, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
        const calendarEl = document.getElementById('sched-calendar');
        const toggleButtons = document.querySelectorAll('.sched-toggle-btn');

        const dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

        function buildCalendar(view) {
            calendarEl.innerHTML = '';

            const today = new Date();
            let start = new Date(today);
            let end = new Date(today);

            if (view === 'week') {
                // Start from Sunday of this week
                const day = start.getDay();
                start.setDate(start.getDate() - day);
                end = new Date(start);
                end.setDate(start.getDate() + 6);
            } else {
                // Month view: first to last day of current month
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            }

            const cells = [];
            const cursor = new Date(start);
            while (cursor <= end) {
                cells.push(new Date(cursor));
                cursor.setDate(cursor.getDate() + 1);
            }

            // Map schedules by day name
            function schedulesForDate(date) {
                const isoDate = date.toISOString().slice(0, 10);
                const dayName = dayNames[date.getDay()].toLowerCase();
                const result = [];

                schedules.forEach(s => {
                    const from = s.valid_from || isoDate;
                    const to   = s.valid_to   || isoDate;
                    if (isoDate >= from && isoDate <= to) {
                        const days = (s.days || []).map(d => d.toLowerCase());
                        if (days.indexOf(dayName) !== -1) {
                            result.push(s);
                        }
                    }
                });

                return result;
            }

            const grid = document.createElement('div');
            grid.className = 'sched-calendar-grid';

            // Headers (Sun..Sat)
            dayNames.forEach(name => {
                const h = document.createElement('div');
                h.className = 'sched-calendar-day-header';
                h.textContent = name.substr(0, 3);
                grid.appendChild(h);
            });

            // Align first cell for week/month
            let leadingEmpty = cells[0].getDay();
            for (let i = 0; i < leadingEmpty; i++) {
                const empty = document.createElement('div');
                empty.className = 'sched-calendar-cell';
                grid.appendChild(empty);
            }

            cells.forEach(date => {
                const cell = document.createElement('div');
                cell.className = 'sched-calendar-cell';

                const dateLabel = document.createElement('div');
                dateLabel.className = 'sched-calendar-date';
                dateLabel.textContent = date.getDate();
                cell.appendChild(dateLabel);

                const daySchedules = schedulesForDate(date);
                daySchedules.forEach(s => {
                    const badge = document.createElement('div');
                    badge.className = 'sched-badge';
                    badge.textContent = s.shift_name || 'Shift';
                    // Color code based on created_by
                    if (s.created_by === 'doctor') {
                        badge.style.background = '#10b981'; // Green for doctor-created
                    } else {
                        badge.style.background = '#16a34a'; // Default green for admin-created
                    }
                    cell.appendChild(badge);

                    const entry = document.createElement('div');
                    entry.className = 'sched-entry';
                    const timeRange = (s.start_time && s.end_time) ? (s.start_time.substr(0,5) + ' - ' + s.end_time.substr(0,5)) : '';
                    const creator = s.created_by === 'doctor' ? ' (Doctor)' : ' (Admin)';
                    entry.textContent = s.full_name + (timeRange ? (' • ' + timeRange) : '') + creator;
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

        // initial render
        buildCalendar('week');
    })();
</script>

<?= $this->endSection() ?>
