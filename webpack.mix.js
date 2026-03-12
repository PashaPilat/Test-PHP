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
// Основной бандл: JS + SCSS
mix.js('resources/js/app.js', 'assets/js')
  .sass('resources/scss/app.scss', 'assets/css')
  .options({ processCssUrls: false })
  .version();

// Копирование картинок
mix.copyDirectory('resources/images', 'public/assets/images');

// Копирование шрифтов (если нужно)
//mix.copyDirectory('resources/css/font_Gotham_Pro', 'public/assets/css/font_Gotham_Pro');

mix.browserSync({
  proxy: 'php-test.loc', // твой локальный домен
  files: [
    'public/assets/js/*.js',
    'public/assets/css/*.css',
    'resources/views/**/*.php'
  ],
  injectChanges: true,
  reload: true
});

