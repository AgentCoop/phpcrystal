let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

////
//// Frontend
////

// Bundle up application plain CSS files
mix.styles([
    'resources/views/frontend/**/*.css'
], 'public/css/frontend/all.css');

if (mix.inProduction()) {
    mix.version();
}

mix.autoload({
    jquery: ['$', 'window.jQuery']
});
