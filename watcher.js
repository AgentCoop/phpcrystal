#! /usr/bin/node

var watch = require('node-watch');
const { exec } = require('child_process');

watch('./modules', { recursive: true }, function(evt, name) {
    if (evt != 'update') {
        return;
    }

    console.time('build');

    if (/Http\/Controllers/.test(name)) {
        var matches = /modules\/([a-zA-Z0-9_]+)/.exec(name);
        var module_name = matches[1];

        console.info('Application controller has been modified, re-building module ' + module_name + '...');

        exec('php artisan package:build --module=' + module_name);

    } else if (/manifest\.php/.test(name)) {
        exec('php artisan package:build');
    }

    console.timeEnd('build');
});