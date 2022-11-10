const mix = require('laravel-mix');
let webpack = require('webpack');
//let fs = require('fs');
require('laravel-mix-purgecss');
require("babel-polyfill");
//const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

module.exports = {
    entry: ["babel-polyfill", "./app.js"]
};
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

let extract = [
  /*'jquery',*/
    'vue',
  /*'bootstrap',*/
    'axios'
  /*'summernote',*/
];

mix.autoload({
    /*jquery: ['$', 'jQuery', 'jquery'],*/
    /*'popper.js/dist/umd/popper.js': ['Popper']*/
});

mix.webpackConfig({
    stats: {
        warnings: false,
    },
    plugins: [
        //new BundleAnalyzerPlugin()
    ]
});

mix.sass('resources/sass/app.scss', 'public/css1')
    .js('resources/js/app.js', 'public/js1')
    .vue({ version: 2 })
    .copy('resources/js/jquery-3.6.0.min.js', 'public/js1')
    //.js('resources/js/admin.js', 'public/js1')
    //.js('resources/js/home.js', 'public/js1')
    //.js('resources/js/faq.js', 'public/js1')
    //.js('resources/js/user.js', 'public/js1')
    //.js('resources/js/spotify.js', 'public/js1')
    //.js('resources/js/spotifystatistics.js', 'public/js1')
    //.js('resources/js/package.js', 'public/js1')
    //.js('resources/js/media.js', 'public/js1')
    //.js('resources/js/fileupload.js', 'public/js1')
    //.js('resources/js/statistics.js', 'public/js1')
    //.js('resources/js/subscribers.js', 'public/js1')
    //.js('resources/js/feeds.js', 'public/js1')
//.js('resources/js/feed_wizard.js', 'public/js1')
    //.js('resources/js/state.js', 'public/js1')
    //.js('resources/js/submit.js', 'public/js1')
/*.babel(['public/js/app.js'], 'public/js/all.js') */
/*    .copy('node_modules/summernote/dist/summernote.css', 'public/css1/summernote.min.css')
    .copyDirectory('node_modules/summernote/dist/font', 'public/css1/font')*/
  .extract()
  //.extract(extract)
  .copyDirectory('resources/css', 'public/css1/vendor')
    .copyDirectory('resources/fonts', 'public/fonts')
    .copyDirectory('resources/images', 'public/images1')
    .copyDirectory('resources/pdf', 'public/pdf1')
    .copyDirectory('resources/assets', 'public/assets')
    //.copyDirectory('resources/js/player', 'public/js1/player')
  .styles([
    'public/css1/app.css',
    'public/css1/vendor/entypo.css',
    'public/css1/vendor/socicon.css',
  ], 'public/css1/all.min.css')
    /*.purgeCss()*/;

if (mix.inProduction()) {
    mix.disableNotifications();
    mix.version();
}
