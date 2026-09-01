<div class="auth-page">
    <div class="auth-container">
        <!-- Logo -->
        <div class="auth-logo">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 22V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h1>DDWB</h1>
            <p>DingeDieWirBesitzen</p>
        </div>

        <!-- Login Form -->
        <div class="auth-card">
            <h2>Anmelden</h2>
            
            <form method="POST" action="<?= route('login.post') ?>" class="form" id="login-form">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label for="email" class="form-label">E-Mail-Adresse</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 4H20C21.1046 4 22 4.89543 22 6V18C22 19.1046 21.1046 20 20 20H4C2.89543 20 2 19.1046 2 18V6C2 4.89543 2.89543 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input <?= isset($errors['email']) ? 'form-input-error' : '' ?>"
                            placeholder="E-Mail-Adresse eingeben"
                            value="<?= e(old('email', '')) ?>"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <span class="form-error"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Passwort</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H19C19.5304 3 20 3.46957 20 4V20C20 20.5304 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="11" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input <?= isset($errors['password']) ? 'form-input-error' : '' ?>"
                            placeholder="Passwort eingeben"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="form-input-toggle" onclick="togglePasswordVisibility()" aria-label="Passwort anzeigen">
                            <svg class="password-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 5C12 5 12 5 12 5C12 5 14.5 7 18 9C19.3333 9.66667 20 10.6 20 12C20 13.4 19.3333 14.3333 18 15C16.5 16 12 18 12 18C12 18 7.5 16 6 15C4.66667 14.3333 4 13.4 4 12C4 10.6 4.66667 9.66667 6 9C9.5 7 12 5 12 5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <svg class="password-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: none;">
                                <path d="M13.875 13.875L18.364 18.364M19.5 12.5C19.5 16.0137 16.5304 19 12.987 19H12.013C8.46957 19 5.5 16.0137 5.5 12.5C5.5 8.9863 8.46957 6 12.013 6H12.987C16.5304 6 19.5 8.9863 19.5 12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.5 8.5L14.5 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M17.8 17.8L16.364 16.364" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M19.5 12.5C19.5 12.5 19.5 12.5 19.5 12.5C19.5 8.9863 16.5304 6 12.987 6H12.013C8.46957 6 5.5 8.9863 5.5 12.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 2C16.9706 2 21 6.02944 21 11C21 15.9706 16.9706 20 12 20C7.02944 20 3 15.9706 3 11C3 6.02944 7.02944 2 12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <span class="form-error"><?= e($errors['password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group form-group-remember">
                    <label class="form-checkbox">
                        <input type="checkbox" name="remember" id="remember" <?= old('remember', false) ? 'checked' : '' ?>>
                        <span class="form-checkbox-checkmark"></span>
                        <span class="form-checkbox-label">Angemeldet bleiben</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 3H9C8.46957 3 8 3.46957 8 4V6H16V4C16 3.46957 15.5304 3 15 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 13H9C8.46957 13 8 13.4696 8 14V20C8 20.5304 8.46957 21 9 21H15C15.5304 21 16 20.5304 16 20V14C16 13.4696 15.5304 13 15 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 13V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 13V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Anmelden
                </button>
            </form>

            <!-- Forgot Password Link -->
            <div class="auth-footer">
                <a href="#" class="auth-link">Passwort vergessen?</a>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="auth-demo">
            <h3>Demo-Zugangsdaten</h3>
            <div class="auth-demo-credentials">
                <div class="auth-demo-item">
                    <span class="auth-demo-label">Admin:</span>
                    <span class="auth-demo-value">admin@ddwb.local</span>
                    <span class="auth-demo-value">password</span>
                </div>
                <div class="auth-demo-item">
                    <span class="auth-demo-label">Benutzer:</span>
                    <span class="auth-demo-value">user@ddwb.local</span>
                    <span class="auth-demo-value">password</span>
                </div>
            </div>
            <p class="auth-demo-warning">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                    <line x1="12" y1="8" x2="12" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <line x1="12" y1="16" x2="12.01" y2="16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Bitte ändern Sie diese Zugangsdaten nach der Installation!
            </p>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.password-eye');
    const eyeOffIcon = document.querySelector('.password-eye-off');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.style.display = 'none';
        eyeOffIcon.style.display = 'block';
    } else {
        passwordInput.type = 'password';
        eyeIcon.style.display = 'block';
        eyeOffIcon.style.display = 'none';
    }
}

// Focus on email input when page loads
document.addEventListener('DOMContentLoaded', function() {
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.focus();
    }
});
</script>
