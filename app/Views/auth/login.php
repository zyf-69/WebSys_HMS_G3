<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
    <div class="auth-title">Sign in to HMS System</div>
    <div class="auth-subtitle">Use your hospital account to access the Hospital Management System.</div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash-message flash-error">
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash-message flash-success">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <form action="/WebSys_HMS_G3/login" method="post" id="login-form">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="email">Email address</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= old('email') ?>"
                placeholder="name@example.com"
                required
            >
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
            >
        </div>

        <div class="form-row">
            <label class="checkbox">
                <input type="checkbox" name="remember" value="1">
                <span>Remember this device</span>
            </label>
            <span style="color:#6a737d;">Admin & staff only</span>
        </div>

        <button type="submit" class="btn-primary" id="login-submit">Sign in</button>

        <div class="auth-footer-text">
            Having trouble signing in? Contact the IT department of St. Peter Hospital.
        </div>
    </form>

<script>
// Ensure form submits properly
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const submitBtn = document.getElementById('login-submit');
    
    if (form && submitBtn) {
        // Log form action for debugging
        console.log('Login form action:', form.action);
        console.log('Login form method:', form.method);
        console.log('Form element:', form);
        
        // Remove any existing event listeners by cloning the form
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        
        // Re-get form reference
        const loginForm = document.getElementById('login-form');
        const loginSubmit = document.getElementById('login-submit');
        
        if (loginForm && loginSubmit) {
            loginForm.addEventListener('submit', function(e) {
                // CRITICAL: Do NOT prevent default - let form submit normally
                console.log('=== FORM SUBMISSION STARTED ===');
                console.log('Form action:', loginForm.action);
                console.log('Form method:', loginForm.method);
                console.log('Email:', document.getElementById('email').value);
                console.log('Password length:', document.getElementById('password').value.length);
                
                // Verify CSRF token exists
                const csrfInput = loginForm.querySelector('input[name="csrf_test_name"]');
                if (csrfInput) {
                    console.log('CSRF token found:', csrfInput.value.substring(0, 10) + '...');
                } else {
                    console.error('CSRF token NOT found!');
                }
                
                // Disable button to prevent double submission
                loginSubmit.disabled = true;
                loginSubmit.textContent = 'Signing in...';
                
                // Let form submit normally - NO preventDefault()
                console.log('Form will submit normally...');
            });
        }
    }
});
</script>
<?= $this->endSection() ?>
