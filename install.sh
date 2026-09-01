#!/bin/bash

# =============================================================================
# DDWB - DingeDieWirBesitzen
# Linux Installation Script (NGINX + PHP-FPM + MySQL/MariaDB)
# 
# This script automates the installation of DDWB on a Linux server with:
# - NGINX as web server
# - PHP 8.4+ with FPM
# - MySQL 8+ or MariaDB 10.6+
# - Composer for PHP dependencies
# - Optional: Let's Encrypt SSL certificates
# 
# Usage: bash install.sh
# =============================================================================

set -euo pipefail

# =============================================================================
# Colors and Formatting
# =============================================================================

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

BOLD='\033[1m'
UNDERLINE='\033[4m'

# =============================================================================
# Functions
# =============================================================================

# Print header
print_header() {
    echo -e "${BLUE}${BOLD}
================================================================================
                    DDWB - Installation
================================================================================${NC}\n"
}

# Print section
print_section() {
    echo -e "\n${BLUE}${BOLD}=== $1 ===${NC}\n"
}

# Print info
print_info() {
    echo -e "${CYAN}ℹ $1${NC}"
}

# Print success
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

# Print warning
print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

# Print error
print_error() {
    echo -e "${RED}✗ $1${NC}" >&2
}

# Ask for confirmation
confirm() {
    local prompt="$1"
    local default="$2"
    local answer
    
    if [ "$default" = "y" ] || [ "$default" = "Y" ]; then
        prompt="$prompt [Y/n]"
    else
        prompt="$prompt [y/N]"
    fi
    
    while true; do
        read -p "$prompt: " answer
        answer="${answer:-$default}"
        
        case "$answer" in
            [Yy]* ) return 0;;
            [Nn]* ) return 1;;
            * ) echo -e "${YELLOW}Bitte antworten Sie mit y oder n${NC}"
        esac
    done
}

# Ask for input with default
ask_input() {
    local prompt="$1"
    local default="$2"
    local variable="$3"
    
    if [ -n "$default" ]; then
        read -p "$prompt [$default]: " $variable
        eval "$variable=\"${!variable:-$default}\""
    else
        read -p "$prompt: " $variable
    fi
}

# Check if running as root
check_root() {
    if [ "$(id -u)" -ne 0 ]; then
        print_error "Dieses Skript muss als root ausgeführt werden!"
        print_info "Bitte führen Sie das Skript mit sudo aus: sudo bash install.sh"
        exit 1
    fi
}

# Detect Linux distribution
detect_distro() {
    if [ -f /etc/os-release ]; then
        . /etc/os-release
        DISTRO="$ID"
        DISTRO_VERSION="$VERSION_ID"
    elif type lsb_release >/dev/null 2>&1; then
        DISTRO=$(lsb_release -si | tr '[:upper:]' '[:lower:]')
        DISTRO_VERSION=$(lsb_release -sr)
    elif [ -f /etc/lsb-release ]; then
        . /etc/lsb-release
        DISTRO="$DISTRIB_ID"
        DISTRO_VERSION="$DISTRIB_RELEASE"
    else
        DISTRO=$(uname -s)
        DISTRO_VERSION=$(uname -r)
    fi
    
    echo "$DISTRO"
}

# Install packages
install_packages() {
    local packages=("$@")
    
    case "$DISTRO" in
        ubuntu|debian)
            apt-get update
            apt-get install -y "${packages[@]}"
            ;;
        centos|rhel|almalinux|rocky)
            yum install -y epel-release
            yum install -y "${packages[@]}"
            ;;
        fedora)
            dnf install -y "${packages[@]}"
            ;;
        arch|manjaro)
            pacman -Syu --noconfirm "${packages[@]}"
            ;;
        *)
            print_error "Ununterstützte Distribution: $DISTRO"
            exit 1
            ;;
    esac
}

# Check command existence
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# =============================================================================
# Main Installation
# =============================================================================

print_header

# Check if already installed
if [ -f "/var/www/ddwb/public/index.php" ]; then
    print_warning "DDWB scheint bereits installiert zu sein!"
    confirm "Möchten Sie die Installation fortsetzen?" "n"
    if [ $? -ne 0 ]; then
        echo "Installation abgebrochen."
        exit 0
    fi
fi

# Check root
check_root

# Detect distribution
print_section "Systemprüfung"
DISTRO=$(detect_distro)
print_info "Erkannte Distribution: $DISTRO $DISTRO_VERSION"

# Check for curl
if ! command_exists curl; then
    print_info "Installiere curl..."
    install_packages curl
    print_success "curl installiert"
fi

# =============================================================================
# Ask for installation parameters
# =============================================================================

print_section "Installationsparameter"

# Domain
ask_input "Domain-Name (z.B. ddwb.example.com oder localhost für Entwicklung)" "ddwb.example.com" DOMAIN

# Installation path
ask_input "Installationspfad (z.B. /var/www/ddwb)" "/var/www/ddwb" INSTALL_PATH

# Web root (usually INSTALL_PATH/public)
ask_input "Web-Root-Pfad (public-Verzeichnis)" "$INSTALL_PATH/public" WEB_ROOT

# PHP version
print_info "Verfügbare PHP-Versionen werden geprüft..."
PHP_VERSIONS=()
for version in 8.4 8.3 8.2 8.1 8.0; do
    if command_exists "php$version"; then
        PHP_VERSIONS+=("$version")
    fi
done

if [ ${#PHP_VERSIONS[@]} -eq 0 ]; then
    print_error "Keine PHP-Version gefunden! Bitte installieren Sie PHP 8.0 oder höher."
    exit 1
fi

print_info "Verfügbare PHP-Versionen: ${PHP_VERSIONS[*]}"
ask_input "PHP-Version auswählen" "${PHP_VERSIONS[0]}" PHP_VERSION

# Database type
PS3="Datenbank-Typ auswählen: "
options=("mysql" "mariadb")
select opt in "${options[@]}"; do
    case "$opt" in
        mysql|mariadb)
            DB_TYPE="$opt"
            break
            ;;
        *)
            echo "Ungültige Auswahl. Bitte wählen Sie 1 oder 2."
            ;;
    esac
done

# Database host
ask_input "Datenbank-Host (standardmäßig localhost)" "localhost" DB_HOST

# Database port
ask_input "Datenbank-Port (standardmäßig 3306)" "3306" DB_PORT

# Database name
ask_input "Datenbank-Name" "ddwb" DB_NAME

# Database user
ask_input "Datenbank-Benutzername" "ddwb_user" DB_USER

# Database password (generate random if empty)
read -p "Datenbank-Passwort (leer lassen für zufälliges Passwort): " DB_PASSWORD
if [ -z "$DB_PASSWORD" ]; then
    DB_PASSWORD=$(openssl rand -base64 16)
    print_info "Zufälliges Datenbank-Passwort generiert"
fi

# Admin user
ask_input "Admin-E-Mail" "admin@$DOMAIN" ADMIN_EMAIL
ask_input "Admin-Name" "Administrator" ADMIN_NAME
read -p "Admin-Passwort: " ADMIN_PASSWORD

# SSL Certificate
confirm "Möchten Sie ein SSL-Zertifikat mit Let's Encrypt einrichten?" "y"
USE_SSL=$?

if [ $USE_SSL -eq 0 ]; then
    ask_input "E-Mail für Let's Encrypt" "admin@$DOMAIN" SSL_EMAIL
fi

# =============================================================================
# Install Dependencies
# =============================================================================

print_section "Installiere Abhängigkeiten"

# Required packages
PACKAGES=(
    nginx
    "php$PHP_VERSION-fpm"
    "php$PHP_VERSION-cli"
    "php$PHP_VERSION-mysql"
    "php$PHP_VERSION-gd"
    "php$PHP_VERSION-mbstring"
    "php$PHP_VERSION-openssl"
    "php$PHP_VERSION-fileinfo"
    "php$PHP_VERSION-json"
    "php$PHP_VERSION-curl"
    git
    unzip
    curl
)

# Database packages
if [ "$DB_TYPE" = "mysql" ]; then
    PACKAGES+=("mysql-server")
else
    PACKAGES+=("mariadb-server")
fi

# Install packages
print_info "Installiere Pakete..."
install_packages "${PACKAGES[@]}"
print_success "Pakete installiert"

# Install Composer
if ! command_exists composer; then
    print_info "Installiere Composer..."
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    print_success "Composer installiert"
fi

# =============================================================================
# Configure Database
# =============================================================================

print_section "Konfiguriere Datenbank"

# Start database service
if [ "$DB_TYPE" = "mysql" ]; then
    DB_SERVICE="mysql"
else
    DB_SERVICE="mariadb"
fi

# Ensure database is running
if ! systemctl is-active --quiet $DB_SERVICE; then
    print_info "Starte $DB_SERVICE..."
    systemctl start $DB_SERVICE
    systemctl enable $DB_SERVICE
    print_success "$DB_SERVICE gestartet und aktiviert"
fi

# Create database
print_info "Erstelle Datenbank $DB_NAME..."
mysql -u root <<-EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '\`$DB_USER\`'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.\* TO '\`$DB_USER\`'@'localhost';
FLUSH PRIVILEGES;
EOF
print_success "Datenbank erstellt"

# =============================================================================
# Clone DDWB Repository
# =============================================================================

print_section "Lade DDWB herunter"

if [ ! -d "$INSTALL_PATH/.git" ]; then
    print_info "Klone Repository..."
    git clone https://github.com/TheJoJo1/ddwb.git "$INSTALL_PATH"
    print_success "Repository geklont"
else
    print_info "Repository existiert bereits. Aktualisiere..."
    cd "$INSTALL_PATH"
    git pull origin main
    print_success "Repository aktualisiert"
fi

# =============================================================================
# Configure Application
# =============================================================================

print_section "Konfiguriere Anwendung"

# Set permissions
print_info "Setze Berechtigungen..."
chown -R www-data:www-data "$INSTALL_PATH"
chmod -R 755 "$INSTALL_PATH"
chmod -R 775 "$INSTALL_PATH/storage"
print_success "Berechtigungen gesetzt"

# Create storage directories
mkdir -p "$INSTALL_PATH/storage/logs"
mkdir -p "$INSTALL_PATH/storage/cache"
mkdir -p "$INSTALL_PATH/storage/sessions"
chown -R www-data:www-data "$INSTALL_PATH/storage"

# Copy config files
cd "$INSTALL_PATH"
cp config/config.php.dist config/config.php
cp config/app.php.dist config/app.php
cp config/database.php.dist config/database.php

# Configure database.php
cat > config/database.php <<-EOF
<?php

return [
    'driver' => 'mysql',
    'host' => '$DB_HOST',
    'port' => $DB_PORT,
    'database' => '$DB_NAME',
    'username' => '$DB_USER',
    'password' => '$DB_PASSWORD',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
EOF

# Configure app.php
cat > config/app.php <<-EOF
<?php

return [
    'env' => 'production',
    'debug' => false,
    'timezone' => 'Europe/Berlin',
    'url' => 'https://$DOMAIN',
    
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
EOF

# Configure config.php
cat > config/config.php <<-EOF
<?php

return [
    'app' => [
        'name' => 'DingeDieWirBesitzen',
        'version' => '1.0.0',
        'env' => 'production',
        'debug' => false,
        'timezone' => 'Europe/Berlin',
        'url' => 'https://$DOMAIN',
    ],
    
    'database' => [
        'driver' => 'mysql',
        'host' => '$DB_HOST',
        'port' => $DB_PORT,
        'database' => '$DB_NAME',
        'username' => '$DB_USER',
        'password' => '$DB_PASSWORD',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    
    'session' => [
        'lifetime' => 3600,
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax',
    ],
    
    'csrf' => [
        'token_length' => 32,
        'expires' => 3600,
    ],
    
    'pagination' => [
        'per_page' => 25,
    ],
    
    'qr_code' => [
        'size' => 200,
        'margin' => 10,
    ],
    
    'barcode' => [
        'type' => 'code128',
        'width' => 2,
        'height' => 50,
    ],
    
    'maintenance' => [
        'upcoming_days' => 30,
        'due_days' => 7,
        'overdue_days' => 0,
    ],
    
    'storage' => [
        'logs' => '$INSTALL_PATH/storage/logs',
        'cache' => '$INSTALL_PATH/storage/cache',
        'sessions' => '$INSTALL_PATH/storage/sessions',
    ],
    
    'scanner' => [
        'https_required' => true,
        'localhost_allowed' => true,
    ],
];
EOF

print_success "Konfiguration erstellt"

# =============================================================================
# Install PHP Dependencies
# =============================================================================

print_section "Installiere PHP-Abhängigkeiten"

cd "$INSTALL_PATH"
if [ -f "composer.json" ]; then
    print_info "Installiere Composer-Pakete..."
    composer install --no-dev --optimize-autoloader
    print_success "Composer-Pakete installiert"
else
    print_warning "Keine composer.json gefunden. Überspringe Composer-Installation."
fi

# =============================================================================
# Import Database Schema and Seed Data
# =============================================================================

print_section "Importiere Datenbank"

print_info "Importiere Schema..."
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < database/schema.sql
print_success "Schema importiert"

print_info "Importiere Seed-Daten..."
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" < database/seed.sql
print_success "Seed-Daten importiert"

# Update admin password
print_info "Aktualisiere Admin-Passwort..."
ADMIN_PASSWORD_HASH=$(php -r "echo password_hash('$ADMIN_PASSWORD', PASSWORD_BCRYPT);")
mysql -u "$DB_USER" -p"$DB_PASSWORD" "$DB_NAME" <<-EOF
UPDATE \`users\` SET \`password_hash\` = '$ADMIN_PASSWORD_HASH', \`email\` = '$ADMIN_EMAIL', \`name\` = '$ADMIN_NAME' WHERE \`email\` = 'admin@ddwb.local';
EOF
print_success "Admin-Passwort aktualisiert"

# =============================================================================
# Configure NGINX
# =============================================================================

print_section "Konfiguriere NGINX"

# Copy NGINX configuration
NGINX_CONF="/etc/nginx/sites-available/ddwb"

cat > "$NGINX_CONF" <<-EOF
# DDWB - NGINX Configuration
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;

    root $WEB_ROOT;
    index index.php;

    # Error logging
    error_log /var/log/nginx/ddwb_error.log;
    access_log /var/log/nginx/ddwb_access.log;

    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://unpkg.com; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; frame-src 'self'; object-src 'none';" always;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    # Cache static files
    location ~* \\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)\\( {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Deny access to sensitive files and directories
    location ~ /\\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~* (\\.env|composer\\.json|composer\\.lock|\\.git|\\.htaccess|\\.htpasswd|README\\.md|LICENSE) {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Deny access to core, config, database, storage, vendor directories
    location ~* /(config|core|database|storage|vendor|modules/.*/(routes|controllers|models|services|repositories))/ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Deny PHP files in public directory (except index.php)
    location ~* /public/.+\\.php\\$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Front controller - route all requests through index.php
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    # PHP-FPM configuration
    location ~ \\.php\\$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php$PHP_VERSION-fpm.sock;
        
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param PATH_INFO \$fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED \$document_root\$fastcgi_path_info;
        
        fastcgi_buffer_size 16k;
        fastcgi_buffers 4 16k;
        fastcgi_busy_buffers_size 24k;
        fastcgi_temp_file_write_size 256k;
        
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        fastcgi_connect_timeout 300;
    }

    # Prevent directory listing
    autoindex off;

    # Error pages
    error_page 403 /403;
    error_page 404 /404;
    error_page 500 502 503 504 /500;
}
EOF

# Create symlink
if [ ! -f "/etc/nginx/sites-enabled/ddwb" ]; then
    ln -s "$NGINX_CONF" /etc/nginx/sites-enabled/ddwb
    print_success "NGINX-Konfiguration erstellt"
else
    print_info "NGINX-Konfiguration existiert bereits. Überspringe."
fi

# Test NGINX configuration
print_info "Teste NGINX-Konfiguration..."
if nginx -t 2>&1; then
    print_success "NGINX-Konfiguration ist gültig"
else
    print_error "NGINX-Konfiguration enthält Fehler!"
    print_info "Bitte überprüfen Sie die Konfiguration in $NGINX_CONF"
    exit 1
fi

# Restart NGINX
print_info "Starte NGINX neu..."
systemctl restart nginx
systemctl enable nginx
print_success "NGINX neu gestartet und aktiviert"

# =============================================================================
# Configure PHP-FPM
# =============================================================================

print_section "Konfiguriere PHP-FPM"

PHP_FPM_SERVICE="php$PHP_VERSION-fpm"

# Check if PHP-FPM pool exists
PHP_FPM_POOL="/etc/php/$PHP_VERSION/fpm/pool.d/ddwb.conf"

if [ ! -f "$PHP_FPM_POOL" ]; then
    cat > "$PHP_FPM_POOL" <<-EOF
[ddwb]
user = www-data
group = www-data
listen = /run/php/php$PHP_VERSION-fpm-ddwb.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 10
pm.max_requests = 500
chdir = $WEB_ROOT
php_admin_value[error_log] = /var/log/php$PHP_VERSION-fpm-ddwb.log
php_admin_flag[log_errors] = on
php_value[session.save_path] = $INSTALL_PATH/storage/sessions
EOF
    print_success "PHP-FPM Pool erstellt"
else
    print_info "PHP-FPM Pool existiert bereits. Überspringe."
fi

# Restart PHP-FPM
print_info "Starte PHP-FPM neu..."
systemctl restart "$PHP_FPM_SERVICE"
systemctl enable "$PHP_FPM_SERVICE"
print_success "PHP-FPM neu gestartet und aktiviert"

# =============================================================================
# SSL Configuration (Optional)
# =============================================================================

if [ $USE_SSL -eq 0 ]; then
    print_section "Konfiguriere SSL-Zertifikat"
    
    # Check for certbot
    if ! command_exists certbot; then
        print_info "Installiere certbot..."
        case "$DISTRO" in
            ubuntu|debian)
                apt-get install -y certbot python3-certbot-nginx
                ;;
            centos|rhel|almalinux|rocky)
                yum install -y certbot python3-certbot-nginx
                ;;
            fedora)
                dnf install -y certbot python3-certbot-nginx
                ;;
            *)
                print_error "certbot-Installation für $DISTRO nicht unterstützt"
                ;;
        esac
        print_success "certbot installiert"
    fi
    
    # Check if domain is accessible
    print_info "Prüfe Domain-Erreichbarkeit..."
    if ! curl -s -o /dev/null -I -w "%{http_code}" "http://$DOMAIN" | grep -q "200\|301\|302"; then
        print_warning "Domain $DOMAIN ist nicht erreichbar. SSL-Zertifikat kann nicht automatisch installiert werden."
        print_info "Bitte stellen Sie sicher, dass:"
        print_info "1. Die Domain auf diesen Server zeigt"
        print_info "2. Port 80 offen ist"
        print_info "3. NGINX läuft (systemctl status nginx)"
        print_info "Sie können SSL später manuell mit certbot einrichten:"
        print_info "  certbot --nginx -d $DOMAIN -d www.$DOMAIN --email $SSL_EMAIL"
    else
        print_info "Richte SSL-Zertifikat ein..."
        if certbot --nginx -d "$DOMAIN" -d "www.$DOMAIN" --email "$SSL_EMAIL" --non-interactive --agree-tos; then
            print_success "SSL-Zertifikat erfolgreich installiert"
            
            # Set up automatic renewal
            if [ ! -f "/etc/cron.d/certbot" ]; then
                echo "0 */12 * * * root /usr/bin/certbot renew --quiet" > /etc/cron.d/certbot
                print_success "Automatische Zertifikatsverlängerung eingerichtet"
            fi
        else
            print_warning "SSL-Zertifikat konnte nicht automatisch installiert werden."
            print_info "Bitte versuchen Sie es manuell:"
            print_info "  certbot --nginx -d $DOMAIN -d www.$DOMAIN --email $SSL_EMAIL"
        fi
    fi
fi

# =============================================================================
# Final Setup
# =============================================================================

print_section "Abschließende Einrichtung"

# Create .env file (optional)
if [ ! -f "$INSTALL_PATH/.env" ]; then
    cat > "$INSTALL_PATH/.env" <<-EOF
APP_ENV=production
APP_DEBUG=false
APP_URL=https://$DOMAIN

DB_CONNECTION=mysql
DB_HOST=$DB_HOST
DB_PORT=$DB_PORT
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASSWORD

SESSION_DRIVER=file
SESSION_LIFETIME=120

CSRF_TOKEN_LIFETIME=60

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=hello@$DOMAIN
MAIL_FROM_NAME="DDWB"
EOF
    chown www-data:www-data "$INSTALL_PATH/.env"
    chmod 640 "$INSTALL_PATH/.env"
    print_success ".env-Datei erstellt"
fi

# =============================================================================
# Summary
# =============================================================================

print_section "Installation abgeschlossen"

echo -e "\n${GREEN}${BOLD}================================================================================${NC}"
echo -e "${GREEN}${BOLD}                    DDWB - Installation erfolgreich!${NC}"
echo -e "${GREEN}${BOLD}================================================================================${NC}\n"

echo -e "${CYAN}Installationsdetails:${NC}"
echo ""
echo -e "  Domain:          ${BOLD}$DOMAIN${NC}"
echo -e "  Installationspfad: ${BOLD}$INSTALL_PATH${NC}"
echo -e "  Web-Root:        ${BOLD}$WEB_ROOT${NC}"
echo -e "  PHP-Version:     ${BOLD}$PHP_VERSION${NC}"
echo -e "  Datenbank:       ${BOLD}$DB_TYPE://$DB_HOST:$DB_PORT/$DB_NAME${NC}"
echo -e "  Datenbank-Benutzer: ${BOLD}$DB_USER${NC}"
echo -e "  Datenbank-Passwort: ${BOLD}$DB_PASSWORD${NC}"
echo ""
echo -e "${CYAN}Admin-Zugang:${NC}"
echo ""
echo -e "  E-Mail:          ${BOLD}$ADMIN_EMAIL${NC}"
echo -e "  Name:            ${BOLD}$ADMIN_NAME${NC}"
echo -e "  Passwort:        ${BOLD}(das von Ihnen eingegebene Passwort)${NC}"
echo ""

if [ $USE_SSL -eq 0 ]; then
    echo -e "${CYAN}SSL-Zertifikat:${NC}"
    echo ""
    if [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
        echo -e "  Status:          ${GREEN}Installiert${NC}"
        echo -e "  Zertifikat:      ${BOLD}/etc/letsencrypt/live/$DOMAIN/fullchain.pem${NC}"
        echo -e "  Ablaufdatum:     ${BOLD}$(certbot certificates 2>/dev/null | grep "$DOMAIN" -A 5 | grep "Expiry" | awk '{print $2}')${NC}"
    else
        echo -e "  Status:          ${YELLOW}Nicht installiert${NC}"
        echo -e "  Hinweis:          Führen Sie manuell aus: certbot --nginx -d $DOMAIN --email $SSL_EMAIL"
    fi
    echo ""
fi

echo -e "${CYAN}Verfügbare URLs:${NC}"
echo ""
if [ $USE_SSL -eq 0 ] && [ -f "/etc/letsencrypt/live/$DOMAIN/fullchain.pem" ]; then
    echo -e "  Haupt-URL:       ${BOLD}https://$DOMAIN${NC}"
    echo -e "  Mit www:         ${BOLD}https://www.$DOMAIN${NC}"
else
    echo -e "  Haupt-URL:       ${BOLD}http://$DOMAIN${NC}"
    echo -e "  Mit www:         ${BOLD}http://www.$DOMAIN${NC}"
fi
echo -e "  Admin-Login:     ${BOLD}http://$DOMAIN/login${NC}"
echo ""

echo -e "${CYAN}Dienste:${NC}"
echo ""
echo -e "  NGINX:           ${BOLD}$(systemctl is-active nginx 2>/dev/null || echo 'nicht aktiv')${NC}"
echo -e "  PHP-FPM:         ${BOLD}$(systemctl is-active "$PHP_FPM_SERVICE" 2>/dev/null || echo 'nicht aktiv')${NC}"
if [ "$DB_TYPE" = "mysql" ]; then
    echo -e "  MySQL:            ${BOLD}$(systemctl is-active mysql 2>/dev/null || echo 'nicht aktiv')${NC}"
else
    echo -e "  MariaDB:         ${BOLD}$(systemctl is-active mariadb 2>/dev/null || echo 'nicht aktiv')${NC}"
fi
echo ""

echo -e "${GREEN}${BOLD}================================================================================${NC}\n"

echo -e "${YELLOW}WICHTIG:${NC}"
echo ""
echo -e "  1. ${BOLD}Ändern Sie das Admin-Passwort nach dem ersten Login!${NC}"
echo -e "  2. ${BOLD}Sichern Sie die Datenbank-Passwörter!${NC}"
echo -e "  3. ${BOLD}Konfigurieren Sie eine Firewall (z.B. ufw):${NC}"
echo -e "     ufw allow 80/tcp"
echo -e "     ufw allow 443/tcp"
echo -e "     ufw enable"
echo ""

echo -e "${CYAN}Fehlerbehebung:${NC}"
echo ""
echo -e "  - NGINX-Logs:      ${BOLD}/var/log/nginx/ddwb_error.log${NC}"
echo -e "  - PHP-FPM-Logs:    ${BOLD}/var/log/php$PHP_VERSION-fpm-ddwb.log${NC}"
echo -e "  - NGINX testen:    ${BOLD}nginx -t${NC}"
echo -e "  - NGINX neu laden: ${BOLD}systemctl reload nginx${NC}"
echo -e "  - PHP-FPM neu starten: ${BOLD}systemctl restart $PHP_FPM_SERVICE${NC}"
echo ""

echo -e "${GREEN}Die Installation ist abgeschlossen! Viel Spaß mit DDWB!${NC}\n"

exit 0
