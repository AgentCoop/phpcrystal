let mix = require('laravel-mix');

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
