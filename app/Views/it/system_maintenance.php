<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">System Maintenance</div>
        <div class="page-subtitle">Monitor and manage system maintenance tasks and schedules.</div>
    </div>
</div>

<style>
    .maintenance-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        margin-bottom: 20px;
    }
    .maintenance-card h3 {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 16px;
        color: #111827;
    }
    .maintenance-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .maintenance-item {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .maintenance-item:last-child {
        border-bottom: none;
    }
    .maintenance-item:hover {
        background: #f9fafb;
    }
    .maintenance-status {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-scheduled {
        background: #dbeafe;
        color: #1e40af;
    }
    .status-in-progress {
        background: #fef3c7;
        color: #92400e;
    }
    .status-completed {
        background: #dcfce7;
        color: #166534;
    }
    .status-overdue {
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
        margin-bottom: 12px;
        opacity: 0.5;
    }
</style>

<div class="maintenance-card">
    <h3>Maintenance Tasks</h3>
    <ul class="maintenance-list">
        <li class="maintenance-item">
            <div>
                <div style="font-weight: 600; margin-bottom: 4px;">Database Optimization</div>
                <div style="font-size: 12px; color: #6b7280;">Scheduled: Weekly on Sundays at 2:00 AM</div>
            </div>
            <span class="maintenance-status status-scheduled">Scheduled</span>
        </li>
        <li class="maintenance-item">
            <div>
                <div style="font-weight: 600; margin-bottom: 4px;">System Updates Check</div>
                <div style="font-size: 12px; color: #6b7280;">Scheduled: Daily at 6:00 AM</div>
            </div>
            <span class="maintenance-status status-scheduled">Scheduled</span>
        </li>
        <li class="maintenance-item">
            <div>
                <div style="font-weight: 600; margin-bottom: 4px;">Log Cleanup</div>
                <div style="font-size: 12px; color: #6b7280;">Scheduled: Monthly on the 1st at 12:00 AM</div>
            </div>
            <span class="maintenance-status status-scheduled">Scheduled</span>
        </li>
    </ul>
</div>

<div class="maintenance-card">
    <h3>System Health</h3>
    <div style="padding: 16px;">
        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; color: #374151;">Server Status</span>
                <span style="font-weight: 600; color: #059669;">Operational</span>
            </div>
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; width: 100%; background: #10b981;"></div>
            </div>
        </div>
        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; color: #374151;">Database Status</span>
                <span style="font-weight: 600; color: #059669;">Healthy</span>
            </div>
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; width: 100%; background: #10b981;"></div>
            </div>
        </div>
        <div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 13px; color: #374151;">Disk Usage</span>
                <span style="font-weight: 600; color: #6b7280;">45%</span>
            </div>
            <div style="height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden;">
                <div style="height: 100%; width: 45%; background: #3b82f6;"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

