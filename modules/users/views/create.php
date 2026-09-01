<div class="users-create">
    <div class="page-header">
        <h1 class="page-title">Benutzer erstellen</h1>
        
        <div class="page-actions">
            <a href="<?= route('admin.users') ?>" class="btn btn-ghost">
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
            <h2 class="card-title">Neuer Benutzer</h2>
            <p class="card-subtitle">Erstellen Sie einen neuen Benutzer für das System</p>
        </div>

        <div class="card-body">
            <form method="POST" action="<?= route('admin.users.store') ?>" class="form">
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="name" class="form-label">Name *</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 17.9391 19.7893 17.0391 19.4142 16.4142C19.0391 15.7893 18.5304 15.5304 18 15.5304H14C13.4696 15.5304 13 15.7893 12.649 16.1421L12.5 16.25L12.351 16.1421C12 15.7893 11.5304 15.5304 11 15.5304H6C5.46957 15.5304 5 16.4696 5 17.5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 7C16 9.20914 14.2091 11 12 11C9.79086 11 8 9.20914 8 7C8 4.79086 9.79086 3 12 3C14.2091 3 16 4.79086 16 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input <?= isset($errors['name']) ? 'form-input-error' : '' ?>"
                            placeholder="Vollständiger Name"
                            value="<?= e(old('name', '')) ?>"
                            required
                            autofocus
                        >
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <span class="form-error"><?= e($errors['name']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">E-Mail-Adresse *</label>
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
                            placeholder="E-Mail-Adresse"
                            value="<?= e(old('email', '')) ?>"
                            required
                        >
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <span class="form-error"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Passwort *</label>
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
                            placeholder="Passwort (mind. 8 Zeichen)"
                            required
                            minlength="8"
                        >
                        <button type="button" class="form-input-toggle" onclick="togglePasswordVisibility('password')" aria-label="Passwort anzeigen">
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

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Passwort bestätigen *</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 21H5C4.46957 21 4 20.5304 4 20V4C4 3.46957 4.46957 3 5 3H19C19.5304 3 20 3.46957 20 4V20C20 20.5304 19.5304 21 19 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="11" r="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            class="form-input <?= isset($errors['password']) ? 'form-input-error' : '' ?>"
                            placeholder="Passwort bestätigen"
                            required
                            minlength="8"
                        >
                        <button type="button" class="form-input-toggle" onclick="togglePasswordVisibility('password_confirmation')" aria-label="Passwort anzeigen">
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

                <div class="form-group">
                    <label for="role" class="form-label">Rolle *</label>
                    <select id="role" name="role" class="form-select <?= isset($errors['role']) ? 'form-select-error' : '' ?>" required>
                        <option value="">Bitte wählen Sie eine Rolle aus</option>
                        <?php foreach ($roles as $value => $label): ?>
                            <option value="<?= e($value) ?>" <?= (old('role', '') === $value) ? 'selected' : '' ?>>
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <span class="form-error"><?= e($errors['role']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input 
                            type="checkbox" 
                            name="active" 
                            id="active" 
                            <?= old('active', true) ? 'checked' : '' ?>
                        >
                        <span class="form-checkbox-checkmark"></span>
                        <span class="form-checkbox-label">Benutzer aktivieren</span>
                    </label>
                </div>

                <div class="form-actions form-actions-between">
                    <a href="<?= route('admin.users') ?>" class="btn btn-ghost">
                        Abbrechen
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Benutzer erstellen
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
