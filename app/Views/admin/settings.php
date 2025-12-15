<?= $this->extend('layouts/template') ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div>
        <div class="page-title">Settings</div>
        <div class="page-subtitle">Manage system settings and preferences.</div>
    </div>
</div>

<style>
    .settings-container {
        display: grid;
        gap: 20px;
    }
    .settings-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
    }
    .settings-card-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #f3f4f6;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 8px;
    }
    .form-label .required {
        color: #dc2626;
    }
    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        color: #111827;
        background: #ffffff;
        transition: border-color 0.2s;
    }
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #16a34a;
        box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
    }
    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }
    .form-help {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    .btn-primary {
        padding: 10px 20px;
        background: #16a34a;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: #15803d;
    }
    .btn-secondary {
        padding: 10px 20px;
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    .checkbox-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
</style>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= esc(session()->getFlashdata('success')) ?>
    </div>
<?php endif; ?>

<div class="settings-container">
    <!-- General Settings -->
    <div class="settings-card">
        <div class="settings-card-title">General Settings</div>
        <form action="<?= site_url('admin/settings') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="settings_section" value="general">
            
            <div class="form-group">
                <label class="form-label">
                    Hospital Name <span class="required">*</span>
                </label>
                <input type="text" name="hospital_name" class="form-input" 
                       value="<?= esc(old('hospital_name', 'General Hospital')) ?>" 
                       placeholder="Enter hospital name" required>
                <div class="form-help">The official name of your hospital</div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Hospital Address <span class="required">*</span>
                </label>
                <textarea name="hospital_address" class="form-textarea" 
                          placeholder="Enter hospital address" required><?= esc(old('hospital_address', '123 Hospital Street, City, Country')) ?></textarea>
                <div class="form-help">Complete address of your hospital</div>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="hospital_phone" class="form-input" 
                       value="<?= esc(old('hospital_phone', '+1 (555) 123-4567')) ?>" 
                       placeholder="Enter phone number">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="hospital_email" class="form-input" 
                       value="<?= esc(old('hospital_email', 'info@hospital.com')) ?>" 
                       placeholder="Enter email address">
            </div>

            <div class="form-group">
                <label class="form-label">Timezone</label>
                <select name="timezone" class="form-select">
                    <option value="UTC" <?= old('timezone', 'UTC') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                    <option value="Asia/Manila" <?= old('timezone') === 'Asia/Manila' ? 'selected' : '' ?>>Asia/Manila (PHT)</option>
                    <option value="America/New_York" <?= old('timezone') === 'America/New_York' ? 'selected' : '' ?>>America/New_York (EST)</option>
                    <option value="Europe/London" <?= old('timezone') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT)</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="window.location.reload()">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Security Settings -->
    <div class="settings-card">
        <div class="settings-card-title">Security Settings</div>
        <form action="<?= site_url('admin/settings') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="settings_section" value="security">
            
            <div class="form-group">
                <label class="form-label">Minimum Password Length</label>
                <input type="number" name="min_password_length" class="form-input" 
                       value="<?= esc(old('min_password_length', 8)) ?>" min="6" max="32" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password Requirements</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_uppercase" value="1" 
                               <?= old('require_uppercase', 1) ? 'checked' : '' ?>>
                        <span>Require uppercase letters</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_lowercase" value="1" 
                               <?= old('require_lowercase', 1) ? 'checked' : '' ?>>
                        <span>Require lowercase letters</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_numbers" value="1" 
                               <?= old('require_numbers', 1) ? 'checked' : '' ?>>
                        <span>Require numbers</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_symbols" value="1" 
                               <?= old('require_symbols', 0) ? 'checked' : '' ?>>
                        <span>Require special characters</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Session Timeout (minutes)</label>
                <input type="number" name="session_timeout" class="form-input" 
                       value="<?= esc(old('session_timeout', 30)) ?>" min="5" max="480">
                <div class="form-help">Time before user session expires due to inactivity</div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="window.location.reload()">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
