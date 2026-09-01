<div class="devices-edit">
    <div class="page-header">
        <h1 class="page-title">Gerät bearbeiten</h1>
        <p class="page-subtitle">Gerät: <?= e($device['name']) ?> (<code><?= e($device['internal_id']) ?></code>)</p>
    </div>

    <form method="POST" action="<?= route('devices.update', ['id' => $device['id']]) ?>" class="form" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">

        <div class="form-grid">
            <!-- Left Column -->
            <div class="form-grid-column">
                <!-- Name -->
                <div class="form-group <?= has_error('name', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="name" class="form-label required">Name</label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            class="form-input"
                            placeholder="Beispiel: Laptop Dell XPS 15"
                            value="<?= e($old['name'] ?? $device['name']) ?>"
                            required
                        >
                    </div>
                    <?php if (has_error('name', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('name', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="form-group <?= has_error('description', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="description" class="form-label">Beschreibung</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-textarea"
                        placeholder="Detaillierte Beschreibung des Geräts..."
                        rows="3"
                    ><?= e($old['description'] ?? $device['description']) ?></textarea>
                    <?php if (has_error('description', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('description', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Category -->
                <div class="form-group <?= has_error('category_id', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="category_id" class="form-label">Kategorie</label>
                    <select id="category_id" name="category_id" class="form-select">
                        <option value="">Keine Kategorie</option>
                        <?php foreach ($categoryOptions as $option): ?>
                            <option 
                                value="<?= e($option['value']) ?>" 
                                <?= (($old['category_id'] ?? $device['category_id']) === (int)$option['value']) ? 'selected' : '' ?>
                            >
                                <?= e($option['label']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (has_error('category_id', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('category_id', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Serial Number -->
                <div class="form-group <?= has_error('serial_number', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="serial_number" class="form-label">Seriennummer</label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            id="serial_number" 
                            name="serial_number" 
                            class="form-input"
                            placeholder="ABC123XYZ"
                            value="<?= e($old['serial_number'] ?? $device['serial_number']) ?>"
                        >
                    </div>
                    <?php if (has_error('serial_number', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('serial_number', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Status -->
                <div class="form-group <?= has_error('status', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="status" class="form-label required">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option 
                                value="<?= e($value) ?>" 
                                <?= (($old['status'] ?? $device['status']) === $value) ? 'selected' : '' ?>
                            >
                                <?= e($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (has_error('status', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('status', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div class="form-grid-column">
                <!-- Location -->
                <div class="form-group <?= has_error('location', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="location" class="form-label">Standort</label>
                    <div class="form-input-wrapper">
                        <input 
                            type="text" 
                            id="location" 
                            name="location" 
                            class="form-input"
                            placeholder="Raum 101, Regal A"
                            value="<?= e($old['location'] ?? $device['location']) ?>"
                        >
                    </div>
                    <?php if (has_error('location', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('location', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Purchase Date -->
                <div class="form-group <?= has_error('purchase_date', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="purchase_date" class="form-label">Kaufdatum</label>
                    <div class="form-input-wrapper">
                        <input 
                            type="date" 
                            id="purchase_date" 
                            name="purchase_date" 
                            class="form-input"
                            value="<?= e($old['purchase_date'] ?? $device['purchase_date']) ?>"
                        >
                    </div>
                    <?php if (has_error('purchase_date', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('purchase_date', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Purchase Price -->
                <div class="form-group <?= has_error('purchase_price', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="purchase_price" class="form-label">Kaufpreis (€)</label>
                    <div class="form-input-wrapper">
                        <input 
                            type="number" 
                            id="purchase_price" 
                            name="purchase_price" 
                            class="form-input"
                            placeholder="1299.99"
                            step="0.01"
                            min="0"
                            value="<?= e($old['purchase_price'] ?? $device['purchase_price']) ?>"
                        >
                    </div>
                    <?php if (has_error('purchase_price', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('purchase_price', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Warranty Expires -->
                <div class="form-group <?= has_error('warranty_expires', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="warranty_expires" class="form-label">Garantieablauf</label>
                    <div class="form-input-wrapper">
                        <input 
                            type="date" 
                            id="warranty_expires" 
                            name="warranty_expires" 
                            class="form-input"
                            value="<?= e($old['warranty_expires'] ?? $device['warranty_expires']) ?>"
                        >
                    </div>
                    <?php if (has_error('warranty_expires', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('warranty_expires', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>

                <!-- Notes -->
                <div class="form-group <?= has_error('notes', $errors ?? []) ? 'form-group-error' : '' ?>">
                    <label for="notes" class="form-label">Notizen</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        class="form-textarea"
                        placeholder="Zusätzliche Notizen..."
                        rows="3"
                    ><?= e($old['notes'] ?? $device['notes']) ?></textarea>
                    <?php if (has_error('notes', $errors ?? [])): ?>
                        <div class="form-error-message"><?= e(get_error('notes', $errors ?? [])) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 5L5 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Gerät aktualisieren
            </button>
            
            <a href="<?= route('devices.show', ['id' => $device['id']]) ?>" class="btn btn-ghost">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Abbrechen
            </a>
        </div>
    </form>
</div>
