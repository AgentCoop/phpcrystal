#! /usr/bin/node

var watch = require('node-watch');

const { exec } = require('child_process');
const timerPackageBuild = 'package-build';
const timerPackageModule = 'module-build';

watch('./modules', { recursive: true }, function(evt, name) {
    if (evt != 'update') {
        return;
    }

    if (/Http\/Controllers/.test(name)) {
        var matches = /modules\/([a-zA-Z0-9_]+)/.exec(name);
        var module_name = matches[1];

        console.time(timerPackageModule + '-' + module_name);

        exec('php artisan package:build --module=' + module_name);

        console.timeEnd(timerPackageModule + '-' + module_name);
    } else if (/manifest\.php/.test(name)) {
        console.time(timerPackageBuild);

        exec('php artisan package:build');

        console.timeEnd(timerPackageBuild);
    }
});