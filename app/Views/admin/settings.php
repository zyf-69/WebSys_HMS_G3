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
    .alert-success {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
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

<?php 
// Get flashdata messages
$errorFlash = session()->getFlashdata('error');
$successFlash = session()->getFlashdata('success');

// Debug: Log flashdata (remove in production)
if (ENVIRONMENT === 'development') {
    log_message('debug', 'Settings page - Error flash: ' . ($errorFlash ?? 'null'));
    log_message('debug', 'Settings page - Success flash: ' . ($successFlash ?? 'null'));
}
?>
<?php if ($errorFlash): ?>
    <div class="alert alert-error" style="display: block !important;">
        <?= esc($errorFlash) ?>
    </div>
<?php endif; ?>

<?php if ($successFlash): ?>
    <div class="alert alert-success" id="success-message" style="display: block !important;">
        <?= esc($successFlash) ?>
    </div>
    <script>
        // Auto-dismiss success message after 5 seconds
        setTimeout(function() {
            const msg = document.getElementById('success-message');
            if (msg) {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(function() {
                    msg.remove();
                }, 500);
            }
        }, 5000);
    </script>
<?php endif; ?>

<div class="settings-container">
    <!-- General Settings -->
    <div class="settings-card">
        <div class="settings-card-title">General Settings</div>
        <!-- Debug: Form action URL -->
        <script>
            console.log('General form action URL: /WebSys_HMS_G3/admin/settings');
        </script>
        <form action="/WebSys_HMS_G3/admin/settings" method="post" id="general-settings-form" onsubmit="console.log('General form onsubmit fired'); return true;">
            <?= csrf_field() ?>
            <input type="hidden" name="settings_section" value="general">
            
            <div class="form-group">
                <label class="form-label">
                    Hospital Name <span class="required">*</span>
                </label>
                <input type="text" name="hospital_name" class="form-input" 
                       value="<?= esc(old('hospital_name', $general['hospital_name'] ?? 'General Hospital')) ?>" 
                       placeholder="Enter hospital name" required>
                <div class="form-help">The official name of your hospital</div>
            </div>

            <div class="form-group">
                <label class="form-label">
                    Hospital Address <span class="required">*</span>
                </label>
                <textarea name="hospital_address" class="form-textarea" 
                          placeholder="Enter hospital address" required><?= esc(old('hospital_address', $general['hospital_address'] ?? '123 Hospital Street, City, Country')) ?></textarea>
                <div class="form-help">Complete address of your hospital</div>
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="hospital_phone" class="form-input" 
                       value="<?= esc(old('hospital_phone', $general['hospital_phone'] ?? '+1 (555) 123-4567')) ?>" 
                       placeholder="Enter phone number">
            </div>

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="hospital_email" class="form-input" 
                       value="<?= esc(old('hospital_email', $general['hospital_email'] ?? 'info@hospital.com')) ?>" 
                       placeholder="Enter email address">
            </div>

            <div class="form-group">
                <label class="form-label">Timezone</label>
                <select name="timezone" class="form-select">
                    <?php 
                    $currentTimezone = old('timezone', $general['timezone'] ?? 'UTC');
                    $timezones = [
                        'UTC' => 'UTC',
                        'Asia/Manila' => 'Asia/Manila (PHT)',
                        'America/New_York' => 'America/New_York (EST)',
                        'Europe/London' => 'Europe/London (GMT)',
                    ];
                    foreach ($timezones as $value => $label): 
                    ?>
                        <option value="<?= esc($value) ?>" <?= $currentTimezone === $value ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="resetForm(this)">Cancel</button>
                <button type="submit" class="btn-primary" id="general-save-btn" onclick="console.log('General save button clicked'); return true;">Save Changes</button>
            </div>
        </form>
    </div>

    <!-- Security Settings -->
    <div class="settings-card">
        <div class="settings-card-title">Security Settings</div>
        <form action="/WebSys_HMS_G3/admin/settings" method="post" id="security-settings-form" onsubmit="console.log('Security form onsubmit fired'); return true;">
            <?= csrf_field() ?>
            <input type="hidden" name="settings_section" value="security">
            
            <div class="form-group">
                <label class="form-label">Minimum Password Length</label>
                <input type="number" name="min_password_length" class="form-input" 
                       value="<?= esc(old('min_password_length', $security['min_password_length'] ?? 8)) ?>" min="6" max="32" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password Requirements</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_uppercase" value="1" 
                               <?= (old('require_uppercase', $security['require_uppercase'] ?? '1') === '1') ? 'checked' : '' ?>>
                        <span>Require uppercase letters</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_lowercase" value="1" 
                               <?= (old('require_lowercase', $security['require_lowercase'] ?? '1') === '1') ? 'checked' : '' ?>>
                        <span>Require lowercase letters</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_numbers" value="1" 
                               <?= (old('require_numbers', $security['require_numbers'] ?? '1') === '1') ? 'checked' : '' ?>>
                        <span>Require numbers</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="require_symbols" value="1" 
                               <?= (old('require_symbols', $security['require_symbols'] ?? '0') === '1') ? 'checked' : '' ?>>
                        <span>Require special characters</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Session Timeout (minutes)</label>
                <input type="number" name="session_timeout" class="form-input" 
                       value="<?= esc(old('session_timeout', $security['session_timeout'] ?? 30)) ?>" min="5" max="480">
                <div class="form-help">Time before user session expires due to inactivity</div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="resetForm(this)">Cancel</button>
                <button type="submit" class="btn-primary" id="security-save-btn" onclick="console.log('Security save button clicked'); return true;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get both forms
    const generalForm = document.getElementById('general-settings-form');
    const securityForm = document.getElementById('security-settings-form');
    const generalSaveBtn = document.getElementById('general-save-btn');
    const securitySaveBtn = document.getElementById('security-save-btn');

    // Get CSRF token name from the form
    const getCsrfTokenName = function(form) {
        const csrfInput = form.querySelector('input[name^="csrf"]');
        return csrfInput ? csrfInput.name : null;
    };

    // Handle General Settings form submission
    if (generalForm && generalSaveBtn) {
        generalForm.addEventListener('submit', function(e) {
            console.log('General form submitting...');
            console.log('Form action:', generalForm.action);
            console.log('Form method:', generalForm.method);
            
            // Log form data
            const formData = new FormData(generalForm);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            generalSaveBtn.disabled = true;
            generalSaveBtn.textContent = 'Saving...';
            
            // Don't prevent default - let form submit normally
            // Form will submit normally via POST
        });
    }

    // Handle Security Settings form submission
    if (securityForm && securitySaveBtn) {
        securityForm.addEventListener('submit', function(e) {
            console.log('Security form submitting...');
            console.log('Form action:', securityForm.action);
            console.log('Form method:', securityForm.method);
            
            // Log form data
            const formData = new FormData(securityForm);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            securitySaveBtn.disabled = true;
            securitySaveBtn.textContent = 'Saving...';
            
            // Don't prevent default - let form submit normally
            // Form will submit normally via POST
        });
    }
});

// Reset form - redirects to settings page to clear any form state
function resetForm(button) {
    // Simply redirect to settings page to reset form state
    window.location.href = '/WebSys_HMS_G3/admin/settings';
}
</script>

<?= $this->endSection() ?>
