#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}Starting InnovaAI installation...${NC}"

# Get current directory
CURRENT_DIR=$(pwd)

# Check if running inside aaPanel
if [ ! -f "/www/server/panel/class/panelSite.py" ]; then
    echo -e "${YELLOW}Warning: Running without aaPanel${NC}"
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

# Check if .env exists, if not create from example
if [ ! -f ".env" ]; then
    echo -e "${YELLOW}Creating .env file...${NC}"
    cp .env.example .env
fi

# Install composer dependencies
echo -e "${GREEN}Installing Composer dependencies...${NC}"
composer install --no-interaction --prefer-dist --optimize-autoloader

# Generate application key
echo -e "${GREEN}Generating application key...${NC}"
php artisan key:generate

# Clear all caches
echo -e "${GREEN}Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run database migrations
echo -e "${GREEN}Running database migrations...${NC}"
php artisan migrate --force

# Run seeders
echo -e "${GREEN}Running database seeders...${NC}"
php artisan db:seed --class=SystemConfigSeeder
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=AdminUserSeeder

# Install and compile npm dependencies
echo -e "${GREEN}Installing NPM dependencies...${NC}"
npm install
npm run build

# Set up storage link
echo -e "${GREEN}Creating storage link...${NC}"
php artisan storage:link

# Set up Redis
echo -e "${GREEN}Setting up Redis...${NC}"
service redis-server start || service redis start

# Thêm kiểm tra Redis
if ! command -v redis-cli &> /dev/null; then
    echo -e "${YELLOW}Installing Redis...${NC}"
    apt-get update && apt-get install -y redis-server
fi

# Set proper permissions
echo -e "${GREEN}Setting file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Set up supervisor for queue workers
echo -e "${GREEN}Setting up Supervisor...${NC}"
if [ -f "/etc/supervisor/conf.d/innovaai-worker.conf" ]; then
    supervisorctl reread
    supervisorctl update
    supervisorctl restart all
else
    cat > /etc/supervisor/conf.d/innovaai-worker.conf << EOF
[program:innovaai-worker]
process_name=%(program_name)s_%(process_num)02d
command=php ${CURRENT_DIR}/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/html/storage/logs/worker.log
stopwaitsecs=3600
EOF
    supervisorctl reread
    supervisorctl update
    supervisorctl start all
fi

# Thêm kiểm tra Supervisor
if ! command -v supervisord &> /dev/null; then
    echo -e "${YELLOW}Installing Supervisor...${NC}"
    apt-get update && apt-get install -y supervisor
fi

# Set up cron job for scheduled tasks
echo -e "${GREEN}Setting up Cron job...${NC}"
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1") | crontab -

# Optimize
echo -e "${GREEN}Optimizing application...${NC}"
php artisan optimize
php artisan view:cache
php artisan config:cache
php artisan route:cache

# Start/Restart services
echo -e "${GREEN}Restarting services...${NC}"
service nginx restart || systemctl restart nginx
service php8.1-fpm restart || systemctl restart php8.1-fpm
service redis-server restart || service redis restart
supervisorctl reload

echo -e "${GREEN}Installation completed successfully!${NC}"
echo -e "${YELLOW}Please check the documentation for next steps and configuration options.${NC}"

# Display useful information
echo -e "\n${GREEN}Useful commands:${NC}"
echo -e "- View logs: tail -f storage/logs/laravel.log"
echo -e "- Monitor queues: php artisan horizon"
echo -e "- View system config: php artisan system:config list"
echo -e "- Update system config: php artisan system:config set key value"
echo -e "- Clear all caches: php artisan optimize:clear"

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