const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/longevity-stock-chart.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/choices.scss', 'public/css')
    .sass('resources/sass/admin.scss', 'public/css')
    .sourceMaps()
    .copy('resources/fonts', 'public/fonts')
    .copy('resources/images', 'public/images')
    .copy('resources/vendor', 'public/vendor');

if (mix.inProduction()) {
    mix.version();
}
