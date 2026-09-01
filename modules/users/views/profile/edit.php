<div class="profile-edit">
    <div class="page-header">
        <h1 class="page-title">Profil bearbeiten</h1>
        
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
            <h2 class="card-title">Profil bearbeiten</h2>
            <p class="card-subtitle">Ändern Sie Ihre persönlichen Daten</p>
        </div>

        <div class="card-body">
            <form method="POST" action="<?= route('profile.update') ?>" class="form">
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
                            value="<?= e(old('name', $user['name'] ?? '')) ?>"
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
                            value="<?= e(old('email', $user['email'] ?? '')) ?>"
                            required
                        >
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <span class="form-error"><?= e($errors['email']) ?></span>
                    <?php endif; ?>
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
                        Profil aktualisieren
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Passwort ändern</h2>
            <p class="card-subtitle">Ändern Sie Ihr Passwort für mehr Sicherheit</p>
        </div>

        <div class="card-body">
            <a href="<?= route('profile.change_password') ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 10V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M23 13H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 10V4C17 3.46957 16.7893 3.03914 16.4142 2.78929C16.0391 2.53948 15.5304 2.46957 15 2.46957H5C4.46957 2.46957 3.96086 2.53948 3.58579 2.78929C3.21071 3.03914 3 3.46957 3 4V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 10H17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 16H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Passwort ändern
            </a>
        </div>
    </div>
</div>
