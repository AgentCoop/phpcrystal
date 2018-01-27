#! /bin/sh

# Start cron daemon
/usr/sbin/crond

# Logger, max log file size 500Kb
/sbin/syslogd -D -s 500

supervisord

# Create a symlink to the public storage directory
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage

# Fix file system permissions
chgrp -R www-data /var/www/html
chmod -R g+rw /var/www/html

# Run PHP-FPM
php-fpm -F
