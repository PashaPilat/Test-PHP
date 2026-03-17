const mix = require('laravel-mix');
const webpack = require('webpack');

mix.setPublicPath('public');

mix.webpackConfig({
  plugins: [
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery'
    }),
  ],
});

mix.js('resources/js/app.js', 'assets/js')
  .sass('resources/scss/app.scss', 'assets/css')
  .options({ processCssUrls: false })
  .version();

mix.js('resources/js/error.js', 'assets/js')
  .sass('resources/scss/error.scss', 'assets/css')
  .options({ processCssUrls: false })
  .version();

mix.copyDirectory('resources/images', 'public/assets/images');

/* ВОТ ЭТО НУЖНО ДОБАВИТЬ */

mix.copyDirectory(
  'node_modules/@fortawesome/fontawesome-free/webfonts',
  'public/assets/webfonts'
);

mix.browserSync({
  proxy: 'php-test.loc',
  files: [
    'public/assets/js/*.js',
    'public/assets/css/*.css',
    'resources/views/**/*.php'
  ],
  injectChanges: true,
  reload: true
});