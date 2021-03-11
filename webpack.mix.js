const mix = require("laravel-mix");
const path = require("path");
require('mix-env-file');
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

/*
| "-0-" "-0-" "-0-" "-0-" "-0-" "-0-" "-0-" "-0-" "-0-" "-0-" "-0-" "-0-"
| Use the ENV_FILE environment variable to set the .env file to use
| in Webpack bundling your Vue application. The default is the usual
| .env file. e.g. ENV_FILE=.env.production will use the .env.production
| file. This file can contain just the MIX_ constants.
|
*/
mix.env(process.env.ENV_FILE);

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