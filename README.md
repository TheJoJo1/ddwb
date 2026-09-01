# DDWB - DingeDieWirBesitzen

[![PHP 8.4+](https://img.shields.io/badge/PHP-8.4%2B-777BB4?logo=php)](https://www.php.net/)
[![MySQL 8+](https://img.shields.io/badge/MySQL-8%2B-4479A1?logo=mysql)](https://www.mysql.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**DDWB** (DingeDieWirBesitzen) ist eine vollständige, produktionsreife **Inventory-Management-Webanwendung** für die Verwaltung von Geräten, Cases, Ausleihen, Wartungen, Packlisten und Labels.

---

## Inhaltsverzeichnis

- [Funktionen](#funktionen)
- [Technische Anforderungen](#technische-anforderungen)
- [Installation](#installation)
  - [XAMPP/Apache](#xamppapache)
  - [NGINX + PHP-FPM](#nginx--php-fpm)
  - [Docker (optional)](#docker-optional)
- [Konfiguration](#konfiguration)
- [Datenbank-Setup](#datenbank-setup)
- [Verwendete Bibliotheken](#verwendete-bibliotheken)
- [Verzeichnisstruktur](#verzeichnisstruktur)
- [Module](#module)
- [Sicherheit](#sicherheit)
- [API-Dokumentation](#api-dokumentation)
- [Fehlerbehebung](#fehlerbehebung)
- [Mitwirken](#mitwirken)
- [Lizenz](#lizenz)

---

## Funktionen

### Geräteverwaltung (Inventory)
- **CRUD-Operationen**: Erstellen, Lesen, Aktualisieren, Löschen von Geräten
- **Suche & Filter**: Volltextsuche, Filter nach Kategorie, Status, Standort
- **Pagination**: Seitenweise Anzeige großer Datenmengen
- **QR-Codes & Barcodes**: Automatische Generierung für jedes Gerät
- **Soft Delete**: Geräte werden nicht physisch gelöscht, sondern nur markiert
- **Import/Export**: CSV-Import und Export-Funktionen

### Case-Management
- **Container für Geräte**: Gruppierung von Geräten in Cases
- **Zuweisung/Entfernung**: Geräte zu Cases hinzufügen oder entfernen
- **Statusverwaltung**: Status-Tracking für Cases
- **QR-Codes & Barcodes**: Generierung für Cases
- **Label-Druck**: Druckbare Labels für Cases

### Ausleihsystem (Rentals)
- **Ausleihe & Rückgabe**: Transactional integrity für Ausleihvorgänge
- **Fristenverwaltung**: Automatische Berechnung von Rückgabefristen
- **Verlängerungen**: Ausleihen verlängern
- **Status-Tracking**: Aktueller Status (ausgeliehen, zurückgegeben, überfällig)
- **Benachrichtigungen**: Automatische Erinnerungen (geplant)

### Wartungs-Tracking (Maintenance / DGUV3)
- **Prüfintervalle**: Konfigurierbare Prüfintervalle
- **Status-Berechnung**: Automatische Berechnung (OK, anstehend, fällig, überfällig)
- **Historie**: Komplette Wartungshistorie pro Gerät
- **DGUV3-konform**: Unterstützt gesetzliche Prüfpflichten
- **Benachrichtigungen**: Erinnerungen für anstehende Prüfungen

### Packlisten (Packlists)
- **Checklisten**: Erstellen und Verwalten von Packlisten
- **Artikelverwaltung**: Artikel hinzufügen, entfernen, umsortieren
- **Mengenangaben**: Mengen pro Artikel
- **Abhaken**: Artikel als gepackt/ungepackt markieren
- **Druckansicht**: Druckoptimierte Ansicht
- **PDF-Export**: Export als PDF (geplant)

### Label-Generierung
- **Vorlagen**: Konfigurierbare Label-Vorlagen
- **Standardgrößen**: Kleine, mittlere, große Labels, A4
- **QR-Codes & Barcodes**: Integration in Labels
- **Druckoptimiert**: Optimiert für Label-Drucker
- **Vorschau**: Live-Vorschau vor dem Druck

### Mobile Scanner
- **Kamera-basiert**: QR-Code und Barcode-Scannen mit der Kamera
- **Browser-API**: Nutzt `navigator.mediaDevices.getUserMedia()`
- **ZXing-Bibliothek**: Professionelle Decodierung
- **Manuelle Eingabe**: Fallback für Geräte ohne Kamera
- **HTTPS-Unterstützung**: Funktioniert auf HTTPS und localhost

### Audit-Logging
- **Komplette Protokollierung**: Alle wichtigen Aktionen werden geloggt
- **Benutzer-Tracking**: Wer hat was wann gemacht
- **Entity-Tracking**: Welches Objekt wurde verändert
- **Filter**: Logs nach Benutzer, Aktion, Datum filtern
- **Export**: Logs als CSV exportieren

### Dashboard & Statistiken
- **Übersicht**: Wichtige Kennzahlen auf einen Blick
- **Diagramme**: Visuelle Darstellung von Daten
- **Schnellzugriff**: Häufig genutzte Funktionen
- **Benachrichtigungen**: Wichtige Meldungen

### Benutzerverwaltung
- **Rollen**: Admin und Benutzer-Rollen
- **Authentifizierung**: Sichere Passwort-Hashung
- **Sessions**: Sichere Session-Verwaltung
- **CSRF-Schutz**: Schutz vor Cross-Site-Request-Forgery

### Design & UX
- **Responsive**: Optimiert für Desktop, Tablet und Mobile
- **Dark Mode**: Dunkles Design für bessere Lesbarkeit
- **Barrierefreiheit**: WCAG-konform (geplant)
- **Mehrsprachig**: Deutsch (weitere Sprachen geplant)

---

## Technische Anforderungen

### Server
- **PHP**: 8.4 oder höher
- **Datenbank**: MySQL 8.0+ oder MariaDB 10.6+
- **Webserver**: Apache 2.4+ oder NGINX 1.18+
- **PHP-Erweiterungen**:
  - PDO (mit MySQL-Treiber)
  - GD (für Bildbearbeitung)
  - MBString
  - OpenSSL
  - Fileinfo
  - JSON

### Client
- **Browser**: Moderne Browser (Chrome, Firefox, Edge, Safari)
- **Kamera**: Für Scanner-Funktionalität
- **JavaScript**: Aktiviert

---

## Installation

### 1. Projekt herunterladen

```bash
# Klone das Repository
git clone https://github.com/TheJoJo1/ddwb.git
cd ddwb

# Composer-Dependencies installieren (falls Composer verfügbar)
composer install --no-dev --optimize-autoloader
```

> **Hinweis**: Composer ist optional. Die Anwendung funktioniert auch ohne installierte Dependencies, da die wichtigsten Bibliotheken über CDN geladen werden.

### 2. Konfiguration

#### Konfigurationsdateien kopieren

```bash
# config.php aus der Dist-Datei erstellen
cp config/config.php.dist config/config.php
cp config/app.php.dist config/app.php
cp config/database.php.dist config/database.php
```

#### Datenbank konfigurieren

Editieren Sie `config/database.php`:

```php
return [
    'driver' => 'mysql',
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'ddwb',
    'username' => 'ddwb_user',
    'password' => 'your_secure_password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
```

#### Anwendung konfigurieren

Editieren Sie `config/app.php`:

```php
return [
    'env' => 'production', // oder 'development'
    'debug' => false, // true für Entwicklung
    'timezone' => 'Europe/Berlin',
    'url' => 'https://your-domain.com',
    
    'modules' => [
        'users',
        'inventory',
        'cases',
        'rentals',
        'maintenance',
        'packlists',
        'labels',
        'logs',
    ],
];
```

### 3. Webserver konfigurieren

#### XAMPP/Apache

1. Kopieren Sie das Projekt in das `htdocs`-Verzeichnis:
   ```bash
   cp -r ddwb /opt/lampp/htdocs/ddwb
   ```

2. Die `.htaccess`-Datei ist bereits für Apache konfiguriert.

3. Stellen Sie sicher, dass `mod_rewrite` aktiviert ist:
   ```bash
   # In php.ini
   LoadModule rewrite_module modules/mod_rewrite.so
   ```

4. Starten Sie Apache und MySQL:
   ```bash
   sudo /opt/lampp/lampp start
   ```

5. Zugriff: `http://localhost/ddwb`

#### NGINX + PHP-FPM

1. Kopieren Sie das Projekt:
   ```bash
   sudo cp -r ddwb /var/www/ddwb
   sudo chown -R www-data:www-data /var/www/ddwb
   ```

2. Kopieren Sie die NGINX-Konfiguration:
   ```bash
   sudo cp ddwb/nginx.conf /etc/nginx/sites-available/ddwb
   sudo ln -s /etc/nginx/sites-available/ddwb /etc/nginx/sites-enabled/ddwb
   ```

3. Testen und neu laden:
   ```bash
   sudo nginx -t
   sudo systemctl reload nginx
   ```

4. PHP-FPM konfigurieren (falls nicht vorhanden):
   ```bash
   sudo apt install php8.4-fpm
   sudo systemctl start php8.4-fpm
   sudo systemctl enable php8.4-fpm
   ```

5. Zugriff: `http://your-server-ip/ddwb`

#### Docker (optional)

Ein Docker-Setup ist geplant. Bis dahin können Sie die Anwendung manuell installieren.

---

## Datenbank-Setup

### Datenbank erstellen

```sql
-- MySQL/MariaDB
CREATE DATABASE ddwb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ddwb_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON ddwb.* TO 'ddwb_user'@'localhost';
FLUSH PRIVILEGES;
```

### Schema importieren

```bash
# Importieren Sie das Schema
mysql -u ddwb_user -p ddwb < database/schema.sql

# Optional: Seed-Daten importieren (für Entwicklung)
mysql -u ddwb_user -p ddwb < database/seed.sql
```

### Schema-Dateien

- **`database/schema.sql`**: Komplettes Datenbankschema mit:
  - Allen Tabellen (users, devices, cases, rentals, maintenance, packlists, logs, etc.)
  - Foreign Keys und Constraints
  - Indexes für Performance
  - Views für komplexe Abfragen
  - Triggers für automatische Aktionen
  - Stored Procedures

- **`database/seed.sql`**: Beispiel-Daten für:
  - Benutzer (admin, user)
  - Kategorien
  - Geräte
  - Cases
  - Ausleihen
  - Wartungen
  - Packlisten
  - Label-Vorlagen

---

## Verwendete Bibliotheken

### PHP-Bibliotheken (Composer)

| Bibliothek | Zweck | Version |
|------------|-------|---------|
| `endroid/qr-code` | QR-Code-Generierung | ^5.0 |
| `picqer/php-barcode-generator` | Barcode-Generierung | ^2.4 |
| `tecnickcom/tcpdf` | PDF-Generierung | ^6.6 |

### JavaScript-Bibliotheken (CDN)

| Bibliothek | Zweck | CDN |
|------------|-------|-----|
| `@zxing/library` | QR/Barcode-Scannen | unpkg |
| Bootstrap Icons | Icons | CDNJS |

### Installieren der PHP-Bibliotheken

```bash
# Composer installieren (falls nicht vorhanden)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Dependencies installieren
composer install --no-dev --optimize-autoloader
```

> **Hinweis**: Die Anwendung funktioniert auch ohne Composer, da die wichtigsten Funktionen (QR/Barcode-Scannen) über JavaScript-Bibliotheken bereitgestellt werden.

---

## Verzeichnisstruktur

```
ddwb/
├── public/                  # Öffentliches Verzeichnis (Webroot)
│   ├── index.php           # Einstiegspunkt
│   ├── .htaccess           # Apache-Konfiguration
│   └── assets/             # Statische Dateien
│       ├── css/            # Stylesheets
│       ├── js/             # JavaScript
│       │   └── vendor/     # Externe Bibliotheken
│       └── images/         # Bilder
│
├── core/                   # Kernkomponenten
│   ├── Application.php     # Anwendungskern
│   ├── Autoloader.php      # PSR-4 Autoloader
│   ├── Container.php       # Dependency Injection
│   ├── Controller.php      # Basis-Controller
│   ├── Database.php        # Datenbank-Abstraktion
│   ├── Model.php           # Basis-Modell
│   ├── Router.php          # Routing
│   ├── Auth.php            # Authentifizierung
│   ├── Session.php         # Session-Verwaltung
│   ├── Csrf.php            # CSRF-Schutz
│   ├── Logger.php          # Logging
│   ├── Validator.php       # Validierung
│   ├── Response.php        # HTTP-Response
│   ├── Middleware/         # Middleware-Klassen
│   ├── routes.php          # Kern-Routen
│   └── helpers.php         # Hilfsfunktionen
│
├── modules/                # Anwendungsmodule
│   ├── users/              # Benutzerverwaltung
│   │   ├── controllers/
│   │   ├── models/
│   │   ├── routes/
│   │   └── views/
│   │
│   ├── inventory/          # Geräteverwaltung
│   │   ├── controllers/
│   │   ├── models/
│   │   ├── routes/
│   │   └── views/
│   │
│   ├── cases/              # Case-Management
│   ├── rentals/            # Ausleihsystem
│   ├── maintenance/        # Wartungs-Tracking
│   ├── packlists/          # Packlisten
│   ├── labels/             # Label-Generierung
│   └── logs/               # Audit-Logging
│
├── templates/              # Vorlagen
│   ├── layout.php          # Haupt-Layout
│   ├── header.php          # Kopfbereich
│   ├── navigation.php      # Navigation
│   ├── sidebar.php         # Seitenleiste
│   ├── flash.php           # Flash-Messages
│   ├── footer.php          # Fußbereich
│   ├── errors/             # Fehlerseiten
│   │   ├── 403.php
│   │   ├── 404.php
│   │   └── 500.php
│   └── scanner/            # Scanner-Vorlagen
│       └── scanner.php
│
├── config/                 # Konfiguration
│   ├── config.php.dist    # Haupt-Konfiguration
│   ├── app.php.dist        # Anwendungs-Konfiguration
│   └── database.php.dist   # Datenbank-Konfiguration
│
├── database/               # Datenbank
│   ├── schema.sql          # Datenbank-Schema
│   └── seed.sql            # Beispiel-Daten
│
├── storage/                # Speicher (wird erstellt)
│   ├── logs/               # Log-Dateien
│   ├── cache/              # Cache
│   └── sessions/           # Sessions
│
├── vendor/                 # Composer-Dependencies (wird erstellt)
│
├── nginx.conf              # NGINX-Konfiguration
├── README.md               # Diese Datei
└── composer.json           # Composer-Konfiguration
```

---

## Module

### Users-Modul
- Benutzerverwaltung (CRUD)
- Rollen (Admin, Benutzer)
- Profilverwaltung
- Passwort-Reset (geplant)

### Inventory-Modul
- Geräte (Devices)
- Kategorien
- Status-Verwaltung
- Suche & Filter

### Cases-Modul
- Cases als Container für Geräte
- Zuweisung/Entfernung von Geräten
- Status-Tracking
- QR/Barcode-Generierung

### Rentals-Modul
- Ausleihe von Geräten und Cases
- Rückgabe mit Transactional Integrity
- Fristen und Verlängerungen
- Status (ausgeliehen, zurückgegeben, überfällig)

### Maintenance-Modul
- Wartungs-Tracking (DGUV3)
- Prüfintervalle
- Status-Berechnung (OK, anstehend, fällig, überfällig)
- Historie

### Packlists-Modul
- Packlisten erstellen und verwalten
- Artikel hinzufügen/entfernen
- Mengen und Abhaken
- Druckansicht

### Labels-Modul
- Label-Vorlagen
- Standardgrößen
- QR/Barcode-Integration
- Druckoptimierung

### Logs-Modul
- Audit-Trail
- Filter nach Benutzer, Aktion, Datum
- Export

---

## Sicherheit

### Implementierte Sicherheitsmaßnahmen

1. **CSRF-Schutz**: Alle state-changing Requests benötigen CSRF-Tokens
2. **XSS-Schutz**: Alle Benutzereingaben werden escaped (`e()`-Funktion)
3. **SQL-Injection-Schutz**: PDO mit Prepared Statements
4. **Authentifizierung**: Sichere Passwort-Hashung (PHP `password_hash()`)
5. **Autorisierung**: Rollenbasierte Zugriffskontrolle
6. **HTTPS**: Empfohlen für Produktion (localhost-Ausnahme für Entwicklung)
7. **Sicherheitsheader**: X-Content-Type-Options, X-Frame-Options, CSP, etc.
8. **Soft Deletes**: Daten werden nicht physisch gelöscht

### Passwort-Hashung

Die Anwendung verwendet PHP's `password_hash()` und `password_verify()`:
- Algorithmus: bcrypt (Standard)
- Kostenfaktor: 10 (konfigurierbar)

### CSRF-Schutz

- Tokens werden pro Session generiert
- Tokens werden in Hidden-Fields und Headers verwendet
- Tokens sind single-use (für Formulare)

### Empfohlene Produktionseinstellungen

```php
// config/app.php
'env' => 'production',
'debug' => false,

// config/config.php
'csrf' => [
    'token_length' => 32,
    'expires' => 60 * 60, // 1 Stunde
],

'session' => [
    'lifetime' => 60 * 60, // 1 Stunde
    'secure' => true, // Nur über HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
],
```

---

## API-Dokumentation

### API-Endpunkte

| Methode | Endpunkt | Beschreibung | Auth |
|---------|----------|--------------|------|
| GET | `/api/devices` | Liste aller Geräte | Ja |
| GET | `/api/devices/{id}` | Einzelnes Gerät | Ja |
| POST | `/api/devices` | Gerät erstellen | Ja |
| PUT | `/api/devices/{id}` | Gerät aktualisieren | Ja |
| DELETE | `/api/devices/{id}` | Gerät löschen | Ja |
| GET | `/api/cases` | Liste aller Cases | Ja |
| POST | `/api/scanner/resolve` | Identifier auflösen | Ja |

### API-Response-Format

**Erfolg:**
```json
{
    "success": true,
    "data": { ... },
    "message": "Erfolgreich"
}
```

**Fehler:**
```json
{
    "success": false,
    "error": {
        "message": "Fehlermeldung",
        "code": 400
    }
}
```

### Authentifizierung

API-Requests benötigen:
- **Session-Cookie**: Für authentifizierte Benutzer
- **CSRF-Token**: Im Header `X-CSRF-Token` oder im Body

---

## Fehlerbehebung

### Häufige Probleme

#### 1. Scanner funktioniert nicht

- **Problem**: Kamera-Zugriff wird verweigert
- **Lösung**: 
  - Stellen Sie sicher, dass HTTPS verwendet wird (oder localhost)
  - Erlauben Sie Kamera-Zugriff im Browser
  - Verwenden Sie einen modernen Browser (Chrome, Firefox, Edge)

#### 2. Datenbank-Verbindung fehlgeschlagen

- **Problem**: `PDOException: SQLSTATE[HY000] [2002] Connection refused`
- **Lösung**:
  - Prüfen Sie die Datenbank-Konfiguration in `config/database.php`
  - Stellen Sie sicher, dass der MySQL-Server läuft
  - Prüfen Sie Benutzername und Passwort

#### 3. 404 Fehler für alle Seiten

- **Problem**: Alle Seiten zeigen 404
- **Lösung (Apache)**:
  - Stellen Sie sicher, dass `mod_rewrite` aktiviert ist
  - Prüfen Sie die `.htaccess`-Datei
- **Lösung (NGINX)**:
  - Prüfen Sie die NGINX-Konfiguration
  - Führen Sie `nginx -t` aus, um Syntax-Fehler zu finden

#### 4. QR-Codes werden nicht angezeigt

- **Problem**: QR-Codes sind leer
- **Lösung**:
  - Installieren Sie die `endroid/qr-code`-Bibliothek: `composer require endroid/qr-code`
  - Prüfen Sie die PHP-GD-Erweiterung: `php -m | grep gd`

#### 5. PDF-Export funktioniert nicht

- **Problem**: PDF-Export fehlgeschlagen
- **Lösung**:
  - Installieren Sie TCPDF: `composer require tecnickcom/tcpdf`

### Debug-Modus

Aktivieren Sie den Debug-Modus für detaillierte Fehlermeldungen:

```php
// config/app.php
'env' => 'development',
'debug' => true,
```

### Log-Dateien

- **Anwendungs-Logs**: `storage/logs/ddwb-YYYY-MM-DD.log`
- **Webserver-Logs**: `/var/log/nginx/ddwb_error.log` oder `/opt/lampp/logs/error_log`

---

## Mitwirken

### Beiträge sind willkommen!

1. **Forken** Sie das Repository
2. **Erstellen** Sie einen Feature-Branch (`git checkout -b feature/amazing-feature`)
3. **Commiten** Sie Ihre Änderungen (`git commit -m 'Add amazing feature'`)
4. **Pushen** Sie den Branch (`git push origin feature/amazing-feature`)
5. **Öffnen** Sie einen Pull Request

### Code-Richtlinien

- **PHP**: PSR-12 Coding Standard
- **Namespaces**: PSR-4 Autoloading
- **Dokumentation**: PHPDoc für Klassen und Methoden
- **Tests**: Geplant (PHPUnit)

---

## Lizenz

Diese Anwendung steht unter der **MIT-Lizenz**. Siehe [LICENSE](LICENSE) für Details.

---

## Support

- **Dokumentation**: [README.md](README.md)
- **Issues**: [GitHub Issues](https://github.com/TheJoJo1/ddwb/issues)
- **E-Mail**: support@your-domain.com (geplant)

---

## Changelog

### v1.0.0 (geplant)
- Erstes stabiles Release
- Alle Module implementiert
- Komplette Dokumentation

### v0.9.0 (in Entwicklung)
- Scanner-Integration
- NGINX-Konfiguration
- README-Dokumentation

---

**© 2024 DDWB - DingeDieWirBesitzen**
