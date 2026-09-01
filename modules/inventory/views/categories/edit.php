<div class="categories-edit">
    <div class="page-header">
        <h1 class="page-title">Kategorie bearbeiten: <?= e($category['name']) ?></h1>
        
        <div class="page-actions">
            <a href="<?= route('categories.show', ['id' => $category['id']]) ?>" class="btn btn-ghost">
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
            <h2 class="card-title">Kategorie bearbeiten</h2>
            <p class="card-subtitle">Ändern Sie die Kategoriedaten</p>
        </div>

        <div class="card-body">
            <form method="POST" action="<?= route('categories.update', ['id' => $category['id']]) ?>" class="form">
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
                            placeholder="Kategoriename"
                            value="<?= e(old('name', $category['name'] ?? '')) ?>"
                            required
                            autofocus
                        >
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <span class="form-error"><?= e($errors['name']) ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Beschreibung</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-textarea <?= isset($errors['description']) ? 'form-textarea-error' : '' ?>"
                        placeholder="Beschreibung der Kategorie (optional)"
                        rows="3"
                    ><?= e(old('description', $category['description'] ?? '')) ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <span class="form-error"><?= e($errors['description']) ?></span>
                    <?php endif; ?>
                    <p class="form-hint">Maximal 500 Zeichen</p>
                </div>

                <div class="form-group">
                    <label for="parent_id" class="form-label">Übergeordnete Kategorie</label>
                    <select id="parent_id" name="parent_id" class="form-select">
                        <option value="">Keine (Hauptkategorie)</option>
                        <?php foreach ($parentOptions as $id => $name): ?>
                            <option value="<?= e($id) ?>" <?= old('parent_id', $category['parent_id'] ?? '') == $id ? 'selected' : '' ?>>
                                <?= e($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="form-hint">Wählen Sie eine übergeordnete Kategorie aus, um eine Unterkategorie zu erstellen.</p>
                </div>

                <div class="form-group">
                    <label for="sort_order" class="form-label">Sortierreihenfolge</label>
                    <div class="form-input-wrapper">
                        <svg class="form-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 6H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 12H18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 18H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <input 
                            type="number" 
                            id="sort_order" 
                            name="sort_order" 
                            class="form-input"
                            placeholder="Sortierreihenfolge"
                            value="<?= e(old('sort_order', $category['sort_order'] ?? 0)) ?>"
                            min="0"
                        >
                    </div>
                    <p class="form-hint">Kategorien werden nach dieser Zahl sortiert (niedrigere Zahlen zuerst).</p>
                </div>

                <div class="form-group">
                    <label class="form-checkbox">
                        <input 
                            type="checkbox" 
                            name="active" 
                            id="active" 
                            <?= old('active', $category['active'] ?? true) ? 'checked' : '' ?>
                        >
                        <span class="form-checkbox-checkmark"></span>
                        <span class="form-checkbox-label">Kategorie aktiv</span>
                    </label>
                </div>

                <div class="form-actions form-actions-between">
                    <a href="<?= route('categories.show', ['id' => $category['id']]) ?>" class="btn btn-ghost">
                        Abbrechen
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Kategorie aktualisieren
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
