let mix = require('laravel-mix');

////
//// Frontend
////

// Bundle up application plain CSS files
mix.styles([
    'modules/frontoffice/resources/views/**/*.css'
], 'public/css/frontend/all.css');

if (mix.inProduction()) {
    mix.version();
}

mix.autoload({
    jquery: ['$', 'window.jQuery']
});
