#! /usr/bin/node

var watch = require('node-watch');

const { exec } = require('child_process');

function build_time(target, build_callback) {
    console.time(target);
    build_callback();
    console.timeEnd(target)
}

watch('./modules', { recursive: true }, function(evt, name) {
    if (evt != 'update' && evt != 'remove') {
        return;
    }

    if (/Http\/Controllers/.test(name)) {
        build_time('Routes generation', function() {
            exec('php artisan package:build --target=controllers');
        });
    } else if (/Services/.test(name)) {
        build_time('Services generation', function() {
            exec('php artisan package:build --target=services');
        });
    } else if (/manifest\.php/.test(name)) {
        build_time('Package', function() {
            exec('php artisan package:build');
        });
    }
});