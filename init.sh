#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${BLUE}Starting InnovaAI installation on aaPanel...${NC}"

# Get current directory
CURRENT_DIR=$(pwd)

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

# Install Composer if not installed
if ! [ -x "$(command -v composer)" ]; then
    echo -e "${GREEN}Installing Composer...${NC}"
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
fi

# Install Node.js and npm if not installed
if ! [ -x "$(command -v npm)" ]; then
    echo -e "${GREEN}Installing Node.js and npm...${NC}"
    curl -sL https://rpm.nodesource.com/setup_18.x | bash -
    bt install nodejs
fi

# Create .env file if not exists
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Install PHP dependencies
echo -e "${GREEN}Installing PHP dependencies...${NC}"
composer install

# Install Node dependencies
echo -e "${GREEN}Installing Node dependencies...${NC}"
npm install
npm install --save-dev vite

# Generate application key
echo -e "${GREEN}Generating application key...${NC}"
php artisan key:generate

# Create storage link
echo -e "${GREEN}Creating storage link...${NC}"
php artisan storage:link

# Set up database
echo -e "${GREEN}Setting up database...${NC}"
read -p "Enter database name (default: innovaai): " dbname
dbname=${dbname:-innovaai}

read -p "Enter database user (default: root): " dbuser
dbuser=${dbuser:-root}

read -s -p "Enter database password: " dbpass
echo

# Update .env file with database credentials
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$dbname/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$dbuser/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$dbpass/" .env

# Create database if not exists
mysql -u$dbuser -p$dbpass -e "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
echo -e "${GREEN}Running migrations...${NC}"
php artisan migrate

# Run seeders
echo -e "${GREEN}Running seeders...${NC}"
php artisan db:seed --force

# Build assets
echo -e "${GREEN}Building assets...${NC}"
npm run build

# Set permissions
echo -e "${GREEN}Setting permissions...${NC}"
chown -R www:www $CURRENT_DIR
chmod -R 755 $CURRENT_DIR
chmod -R 777 $CURRENT_DIR/storage
chmod -R 777 $CURRENT_DIR/bootstrap/cache

# Install and configure Redis
echo -e "${GREEN}Setting up Redis...${NC}"
if ! [ -x "$(command -v redis-server)" ]; then
    bt install redis
fi

# Install and configure Supervisor through aaPanel
echo -e "${GREEN}Setting up Process Manager...${NC}"
bt install supervisor

# Create Supervisor configuration for queue worker
cat > /www/server/panel/plugin/supervisor/profile/innovaai-worker.conf << EOF
[program:innovaai-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $CURRENT_DIR/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=$CURRENT_DIR
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www
numprocs=8
redirect_stderr=true
stdout_logfile=$CURRENT_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

# Reload Supervisor
supervisorctl reload

# Configure PHP settings
echo -e "${GREEN}Configuring PHP settings...${NC}"
bt php set_session 1800
bt php set_max_execution_time 300
bt php set_max_input_vars 3000
bt php set_memory_limit 256M
bt php set_upload_max_filesize 64M
bt php set_post_max_size 64M

# Optimize Laravel
echo -e "${GREEN}Optimizing Laravel...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Laravel Horizon
echo -e "${GREEN}Starting Laravel Horizon...${NC}"
php artisan horizon

echo -e "${GREEN}Installation completed!${NC}"
echo -e "Please set up your site in aaPanel and point it to: ${BLUE}$CURRENT_DIR/public${NC}"
echo -e "Admin credentials:"
echo -e "Email: ${BLUE}admin@example.com${NC}"
echo -e "Password: ${BLUE}password${NC}"

# Add cron job for Laravel scheduler
echo -e "${GREEN}Adding cron job for Laravel scheduler...${NC}"
(crontab -l 2>/dev/null; echo "* * * * * php $CURRENT_DIR/artisan schedule:run >> /dev/null 2>&1") | crontab -

echo -e "${BLUE}Important:${NC}"
echo -e "1. Set up a new website in aaPanel"
echo -e "2. Point the document root to: $CURRENT_DIR/public"
echo -e "3. Configure SSL if needed"
echo -e "4. Add the following to your Nginx configuration:"
echo -e "${GREEN}"
cat << 'EOF'
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
EOF
echo -e "${NC}"

# Install models directory
mkdir -p storage/app/models
chmod -R 775 storage/app/models
chown -R www-data:www-data storage/app/models

echo "Installation completed!"

# Optimize PHP-FPM
echo -e "${GREEN}Optimizing PHP-FPM...${NC}"
cat > /www/server/php/$PHP_VERSION/etc/php-fpm.conf.d/www.conf << EOF
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
EOF

# Optimize MySQL
echo -e "${GREEN}Optimizing MySQL...${NC}"
cat > /www/server/mysql/my.cnf << EOF
[mysqld]
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
max_connections = 1000
EOF

# Setup monitoring cron jobs
echo -e "${GREEN}Setting up monitoring...${NC}"
(crontab -l 2>/dev/null; echo "*/5 * * * * php $CURRENT_DIR/artisan metrics:collect >> /dev/null 2>&1") | crontab -

# Enable OPcache
echo -e "${GREEN}Enabling OPcache...${NC}"
cat > /www/server/php/$PHP_VERSION/etc/php.d/10-opcache.ini << EOF
zend_extension=opcache.so
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
opcache.enable_cli=1
EOF

# Restart services
service php-fpm restart
service mysql restart
service redis restart
supervisorctl reload 