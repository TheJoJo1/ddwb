<div class="users-index">
    <div class="users-header">
        <div class="users-header-left">
            <h1 class="page-title">Benutzerverwaltung</h1>
            <p class="page-subtitle">Verwaltung aller Benutzer des Systems</p>
        </div>
        
        <div class="users-header-right">
            <a href="<?= route('admin.users.create') ?>" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M5 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Benutzer erstellen
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid" style="margin-bottom: <?= config('spacing.lg', '1.5rem') ?>;">
        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 3C8.84315 3 10.4695 3.53043 11.4142 4.41421C12.3589 5.298 12.8586 6.49138 12.8586 7.85862C12.8586 9.22586 12.3589 10.4192 11.4142 11.303C10.4695 12.1868 8.84315 12.7174 7 12.7174V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['total'] ?? 0) ?></span>
                <span class="stat-card-label">Gesamt</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['active'] ?? 0) ?></span>
                <span class="stat-card-label">Aktiv</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-warning">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17 21V19C17 17.9391 16.7893 17.0391 16.4142 16.4142C16.0391 15.7893 15.5304 15.5304 15 15.5304H9C8.46957 15.5304 7.96086 15.7893 7.58579 16.4142C7.21071 17.0391 7 17.9391 7 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 11C7 10.4696 7.21071 10.0391 7.58579 9.78929C7.96086 9.53948 8.46957 9.46957 9 9.46957H15C15.5304 9.46957 16.0391 9.53948 16.4142 9.78929C16.7893 10.0391 17 10.4696 17 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['inactive'] ?? 0) ?></span>
                <span class="stat-card-label">Inaktiv</span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-icon stat-card-icon-info">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L2 7V17L12 22L22 17V7L12 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 7L12 12L22 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 22V12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="stat-card-content">
                <span class="stat-card-value"><?= e($stats['admin'] ?? 0) ?></span>
                <span class="stat-card-label">Admins</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <form method="GET" action="<?= route('admin.users') ?>" class="filters-form">
            <div class="filters-group">
                <div class="form-group filters-group-item">
                    <label for="search" class="form-label">Suche</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="text" 
                            id="search" 
                            name="search" 
                            class="form-input"
                            placeholder="Name oder E-Mail"
                            value="<?= e($filters['search'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="form-group filters-group-item">
                    <label for="role" class="form-label">Rolle</label>
                    <select id="role" name="role" class="form-select">
                        <option value="">Alle Rollen</option>
                        <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                        <option value="user" <?= ($filters['role'] ?? '') === 'user' ? 'selected' : '' ?>>Benutzer</option>
                    </select>
                </div>

                <div class="form-group filters-group-item">
                    <label for="active" class="form-label">Status</label>
                    <select id="active" name="active" class="form-select">
                        <option value="">Alle Status</option>
                        <option value="1" <?= ($filters['active'] ?? '') === '1' ? 'selected' : '' ?>>Aktiv</option>
                        <option value="0" <?= ($filters['active'] ?? '') === '0' ? 'selected' : '' ?>>Inaktiv</option>
                    </select>
                </div>

                <div class="filters-group-actions">
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="11" cy="11" r="8" stroke="currentColor" stroke-width="2"/>
                            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Filtern
                    </button>
                    
                    <a href="<?= route('admin.users') ?>" class="btn btn-ghost">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Zurücksetzen
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Rolle</th>
                    <th>Status</th>
                    <th>Letzter Login</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            Keine Benutzer gefunden.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= e($user['id']) ?></td>
                            <td>
                                <a href="<?= route('admin.users.show', ['id' => $user['id']]) ?>" class="text-primary">
                                    <?= e($user['name']) ?>
                                </a>
                            </td>
                            <td><?= e($user['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['role'] === 'admin' ? 'error' : 'primary' ?>">
                                    <?= e(ucfirst($user['role'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $user['active'] ? 'success' : 'secondary' ?>">
                                    <?= $user['active'] ? 'Aktiv' : 'Inaktiv' ?>
                                </span>
                            </td>
                            <td>
                                <?= $user['last_login_at'] ? format_date($user['last_login_at'], 'Y-m-d H:i') : '-' ?>
                            </td>
                            <td class="table-actions">
                                <a 
                                    href="<?= route('admin.users.show', ['id' => $user['id']]) ?>" 
                                    class="table-action"
                                    title="Anzeigen"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M14.5 2H6C5.46957 2 5 2.46957 5 3V5C5 5.53043 5.46957 6 6 6H14C14.5304 6 15 5.53043 15 5V3C15 2.46957 14.5304 2 14.5 2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14.5 10H6C5.46957 10 5 10.4696 5 11V19C5 19.5304 5.46957 20 6 20H14.5C15.0304 20 15.5 19.5304 15.5 19V11C15.5 10.4696 15.0304 10 14.5 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14.5 10H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M19 10V19C19 19.5304 18.7893 20.0391 18.4142 20.4142C18.0391 20.7893 17.5304 21 17 21H12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <circle cx="17" cy="14" r="2" stroke="currentColor" stroke-width="2"/>
                                    </svg>
                                </a>
                                
                                <a 
                                    href="<?= route('admin.users.edit', ['id' => $user['id']]) ?>" 
                                    class="table-action"
                                    title="Bearbeiten"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4H4C3.46957 4 3 4.46957 3 5V19C3 19.5304 3.46957 20 4 20H11C11.5304 20 12 19.5304 12 19V13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18 13L23 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M23 8V16C23 16.5304 22.7893 17.0391 22.4142 17.4142C22.0391 17.7893 21.5304 18 21 18H16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18 13L16 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M18 13L20 11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                
                                <a 
                                    href="<?= route('admin.users.toggle_active', ['id' => $user['id']]) ?>" 
                                    class="table-action"
                                    title="<?= $user['active'] ? 'Deaktivieren' : 'Aktivieren' ?>"
                                    onclick="return confirm('Möchten Sie diesen Benutzer wirklich <?= $user['active'] ? 'deaktivieren' : 'aktivieren' ?>?')"
                                >
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M13 3L4 12H10V21L19 12V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M19 12H21C21.5304 12 22 12.4696 22 13V20C22 20.5304 21.5304 21 21 21H19V12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                                
                                <?php if ($user['id'] !== $this->getUserId()): ?>
                                    <a 
                                        href="<?= route('admin.users.delete', ['id' => $user['id']]) ?>" 
                                        class="table-action table-action-danger"
                                        title="Löschen"
                                        onclick="return confirm('Möchten Sie diesen Benutzer wirklich löschen?')"
                                    >
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M8 6V4C8 3.46957 8.46957 3 9 3H15C15.5304 3 16 3.46957 16 4V6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['current_page'] > 1): ?>
                <a 
                    href="<?= route('admin.users', ['page' => $pagination['current_page'] - 1, 'search' => $filters['search'], 'role' => $filters['role'], 'active' => $filters['active']]) ?>" 
                    class="pagination-item"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Zurück
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <?php if ($i === $pagination['current_page']): ?>
                    <span class="pagination-item active"><?= $i ?></span>
                <?php else: ?>
                    <a 
                        href="<?= route('admin.users', ['page' => $i, 'search' => $filters['search'], 'role' => $filters['role'], 'active' => $filters['active']]) ?>" 
                        class="pagination-item"
                    ><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                <a 
                    href="<?= route('admin.users', ['page' => $pagination['current_page'] + 1, 'search' => $filters['search'], 'role' => $filters['role'], 'active' => $filters['active']]) ?>" 
                    class="pagination-item"
                >
                    Nächste
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
