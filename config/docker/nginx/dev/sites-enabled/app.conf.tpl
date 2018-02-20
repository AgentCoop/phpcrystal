server {
    listen 80 default_server;
    listen [::]:80 default_server;

    add_header Access-Control-Allow-Origin *;

    charset utf-8;

    set $web_root           /var/www/html/public;
    root $web_root;

    location / {
        try_files $uri /index.php?$query_string;
    }

    location ~ [^/]\.php(/|$) {
        fastcgi_param SCRIPT_FILENAME   $web_root/index.php;
        include                         fastcgi_params;
        fastcgi_pass                    %project-name%-php:9000;
        fastcgi_split_path_info         ^(.+?\.php)(/.*)$;
        fastcgi_index                   index.php;
    }

    location @maintenance {
        try_files /maintenance.html =503;
    }
}
