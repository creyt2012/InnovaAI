#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}Starting InnovaAI installation...${NC}"

# Get current directory
CURRENT_DIR=$(pwd)

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Error: This script must be run as root${NC}"
    exit 1
fi

# Check if running inside aaPanel
if [ ! -f "/www/server/panel/class/panelSite.py" ]; then
    echo -e "${RED}Error: This script must be run on aaPanel${NC}"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
if [ $(echo "$PHP_VERSION < 8.1" | bc) -eq 1 ]; then
    echo -e "${RED}Error: PHP version must be >= 8.1${NC}"
    exit 1
fi

# Install required PHP extensions
echo -e "${GREEN}Installing PHP extensions...${NC}"
apt-get update
apt-get install -y \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-common \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-json \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-mysql \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-zip \
    php${PHP_VERSION}-redis \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-opcache

# Install/Update Composer
echo -e "${GREEN}Installing Composer...${NC}"
if ! [ -x "$(command -v composer)" ]; then
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer
    php -r "unlink('composer-setup.php');"
fi

# Install project dependencies
echo -e "${GREEN}Installing project dependencies...${NC}"
composer install --no-interaction --optimize-autoloader

# Set up environment file
echo -e "${GREEN}Setting up environment file...${NC}"
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
fi

# Set up storage directory
echo -e "${GREEN}Setting up storage directory...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www:www storage bootstrap/cache
php artisan storage:link

# Set up database
echo -e "${GREEN}Setting up database...${NC}"
php artisan migrate --force

# Create database if not exists
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)
if [ ! -z "$DB_NAME" ]; then
    mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
fi

# Optimize Laravel
echo -e "${GREEN}Optimizing Laravel...${NC}"
php artisan optimize
php artisan view:cache
php artisan config:cache
php artisan route:cache

# Setup Redis
echo -e "${GREEN}Setting up Redis...${NC}"
if ! [ -x "$(command -v redis-cli)" ]; then
    apt-get install -y redis-server
    systemctl enable redis-server
    systemctl start redis-server
fi

# Configure PHP
echo -e "${GREEN}Configuring PHP...${NC}"
cat > /www/server/php/${PHP_VERSION}/etc/php.ini << EOF
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
post_max_size = 50M
upload_max_filesize = 50M
date.timezone = Asia/Ho_Chi_Minh
EOF

# Configure PHP security settings
cat >> /www/server/php/${PHP_VERSION}/etc/php.ini << EOF
expose_php = Off
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
EOF

# Configure OPcache
echo -e "${GREEN}Configuring OPcache...${NC}"
cat > /www/server/php/${PHP_VERSION}/etc/php.d/10-opcache.ini << EOF
zend_extension=opcache.so
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable_cli=1
EOF

# Configure MySQL
echo -e "${GREEN}Configuring MySQL...${NC}"
cat > /www/server/mysql/my.cnf << EOF
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_buffer_size = 16M
innodb_file_per_table = 1
innodb_open_files = 400
innodb_io_capacity = 400
innodb_flush_method = O_DIRECT
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
max_connections = 500
key_buffer_size = 256M
table_open_cache = 400
sort_buffer_size = 4M
read_buffer_size = 4M
read_rnd_buffer_size = 8M
myisam_sort_buffer_size = 64M
thread_cache_size = 8
query_cache_size = 64M
EOF

# Setup monitoring cron jobs
echo -e "${GREEN}Setting up monitoring...${NC}"
(crontab -l 2>/dev/null; echo "*/5 * * * * php $CURRENT_DIR/artisan schedule:run >> /dev/null 2>&1") | crontab -

# Setup Supervisor for queue workers
echo -e "${GREEN}Setting up Supervisor...${NC}"
if ! [ -x "$(command -v supervisord)" ]; then
    apt-get install -y supervisor
fi

cat > /etc/supervisor/conf.d/laravel-worker.conf << EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${CURRENT_DIR}/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www
numprocs=2
redirect_stderr=true
stdout_logfile=${CURRENT_DIR}/storage/logs/worker.log
stopwaitsecs=3600
EOF

supervisorctl reread
supervisorctl update
supervisorctl start all

# Restart services
echo -e "${GREEN}Restarting services...${NC}"
systemctl restart php${PHP_VERSION}-fpm
systemctl restart mysql
systemctl restart redis-server
systemctl restart supervisor

echo -e "${GREEN}Installation completed successfully!${NC}"
echo -e "${BLUE}Please check the documentation for next steps.${NC}"

# Display useful information
echo -e "\n${GREEN}Useful commands:${NC}"
echo "- View Laravel logs: tail -f ${CURRENT_DIR}/storage/logs/laravel.log"
echo "- View worker logs: tail -f ${CURRENT_DIR}/storage/logs/worker.log"
echo "- Restart queue workers: supervisorctl restart all"
echo "- Clear cache: php artisan optimize:clear"

# Backup configurations
echo -e "${GREEN}Backing up configurations...${NC}"
timestamp=$(date +%Y%m%d_%H%M%S)
[ -f /www/server/php/${PHP_VERSION}/etc/php.ini ] && cp /www/server/php/${PHP_VERSION}/etc/php.ini /www/server/php/${PHP_VERSION}/etc/php.ini.${timestamp}
[ -f /www/server/mysql/my.cnf ] && cp /www/server/mysql/my.cnf /www/server/mysql/my.cnf.${timestamp}

# Install required tools
echo -e "${GREEN}Installing required tools...${NC}"
apt-get install -y \
    git \
    unzip \
    curl \
    bc \
    supervisor 

# Test database connection
echo -e "${GREEN}Testing database connection...${NC}"
if ! mysql -e "SELECT 1"; then
    echo -e "${RED}Error: Could not connect to MySQL${NC}"
    exit 1
fi

# Configure Redis security
echo -e "${GREEN}Configuring Redis security...${NC}"
cat > /etc/redis/redis.conf << EOF
bind 127.0.0.1
protected-mode yes
port 6379
tcp-backlog 511
timeout 0
tcp-keepalive 300
daemonize yes
supervised no
EOF

# Function to check command status
check_status() {
    if [ $? -ne 0 ]; then
        echo -e "${RED}Error: $1 failed${NC}"
        exit 1
    fi
}

# Use the function
php artisan migrate --force
check_status "Database migration"

composer install --no-interaction --optimize-autoloader
check_status "Composer installation"

# Cleanup
echo -e "${GREEN}Cleaning up...${NC}"
apt-get clean
rm -rf /var/lib/apt/lists/*

echo -e "\n${BLUE}SSL Configuration:${NC}"
echo "1. Generate SSL certificate using aaPanel"
echo "2. Enable HTTPS in your site configuration"
echo "3. Update .env APP_URL to use https://" 