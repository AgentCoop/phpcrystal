#! /bin/sh

# Start cron daemon
/usr/sbin/crond

# Logger, max log file size 500Kb
/sbin/syslogd -D -s 500

/usr/bin/supervisord -c /etc/supervisord.conf

# Create a symlink to the public storage directory
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Fix file system permissions
chgrp -R www-data /var/www/html
chmod -R g+rw /var/www/html

php /var/www/html/artisan package:build

# Run PHP-FPM in foreground
php-fpm -F
