<div class="change-password">
    <div class="page-header">
        <h1 class="page-title">Passwort ändern</h1>
        
        <div class="page-actions">
            <a href="<?= route('profile') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Zurück
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Passwort ändern</h2>
            <p class="card-subtitle">Geben Sie Ihr aktuelles und Ihr neues Passwort ein</p>
        </div>

        <div class="card-body">
            <form method="POST" action="<?= route('profile.change_password') ?>" class="form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="current_password" class="form-label">Aktuelles Passwort *</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H19C19.5304 3 20 3.46957 20 4V20C20 20.5304 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="11" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input 
                            type="password" 
                            id="current_password" 
                            name="current_password" 
                            class="form-input <?= isset($errors['current_password']) ? 'form-input-error' : '' ?>"
                            placeholder="Aktuelles Passwort"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="form-input-toggle" onclick="togglePasswordVisibility('current_password')" aria-label="Passwort anzeigen">
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
                    <?php if (isset($errors['current_password'])): ?>
                        <span class="form-error"><?= e($errors['current_password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="new_password" class="form-label">Neues Passwort *</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H19C19.5304 3 20 3.46957 20 4V20C20 20.5304 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="11" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            class="form-input <?= isset($errors['new_password']) ? 'form-input-error' : '' ?>"
                            placeholder="Neues Passwort (mind. 8 Zeichen)"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                        <button type="button" class="form-input-toggle" onclick="togglePasswordVisibility('new_password')" aria-label="Passwort anzeigen">
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
                    <?php if (isset($errors['new_password'])): ?>
                        <span class="form-error"><?= e($errors['new_password']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation" class="form-label">Neues Passwort bestätigen *</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H19C19.5304 3 20 3.46957 20 4V20C20 20.5304 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="11" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input 
                            type="password" 
                            id="new_password_confirmation" 
                            name="new_password_confirmation" 
                            class="form-input"
                            placeholder="Neues Passwort bestätigen"
                            autocomplete="new-password"
                        >
                        <button type="button" class="form-input-toggle" onclick="togglePasswordVisibility('new_password_confirmation')" aria-label="Passwort anzeigen">
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
                </div>

                <div class="form-actions form-actions-between">
                    <a href="<?= route('profile') ?>" class="btn btn-ghost">
                        Abbrechen
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Passwort ändern
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const eyeIcon = field.parentNode.querySelector('.password-eye');
    const eyeOffIcon = field.parentNode.querySelector('.password-eye-off');
    
    if (field.type === 'password') {
        field.type = 'text';
        eyeIcon.style.display = 'none';
        eyeOffIcon.style.display = 'block';
    } else {
        field.type = 'password';
        eyeIcon.style.display = 'block';
        eyeOffIcon.style.display = 'none';
    }
}
</script>
