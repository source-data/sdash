const mix = require("laravel-mix");
const path = require("path");

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
mix.webpackConfig({
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources/js/")
        }
    }
});

mix.js("resources/js/main.js", "public/js")
    .vue({ version: 2 })
    .extract(['bootstrap', 'bootstrap-vue'])
    .sourceMaps()
    .sass("resources/sass/app.scss", "public/css")
    .version();

mix.js("resources/js/publicApp.js", "public/js")
.vue({ version: 2 })
.sourceMaps()
.sass("resources/sass/public.scss", "public/css")
.version();