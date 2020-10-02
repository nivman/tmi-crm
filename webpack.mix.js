let glob = require('glob');
let path = require('path');
require('laravel-mix-purgecss');
let mix = require('laravel-mix');
let imageminMozjpeg = require('imagemin-mozjpeg');
let CopyWebpackPlugin = require('copy-webpack-plugin');
let ImageminPlugin = require('imagemin-webpack-plugin').default;
let SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin');

// mix.setPublicPath("public");

mix.extend('serviceWorker', webpackConfig => {
  webpackConfig.plugins.push(
    new SWPrecacheWebpackPlugin({
      cacheId: 'mim',
      filename: 'service-worker.js',
      staticFileGlobsIgnorePatterns: [/\.map$/, /mix-manifest\.json$/, /manifest\.json$/, /service-worker\.js$/],
      staticFileGlobs: ['public/**/*.{js,html,css,png,jpg,gif,svg,eot,svg,ttf,woff,woff2}'],
      minify: true,
      handleFetch: true,
      // skipWaiting: true,
      stripPrefix: 'public/',
      dynamicUrlToDependencies: {
        '/': ['resources/views/home.blade.php'],
      },
      maximumFileSizeToCacheInBytes: 4194304,
      navigateFallbackWhitelist: [/^\/app/],
      runtimeCaching: [
        {
          urlPattern: /^https:\/\/fonts\.googleapis\.com\//,
          handler: 'cacheFirst',
        },
        {
          urlPattern: /\/token/,
          handler: 'networkOnly',
        },
        {
          urlPattern: /\/login/,
          handler: 'networkOnly',
        },
        {
          urlPattern: /\/data/,
          handler: 'networkOnly',
        },
        {
          urlPattern: /\/app\//,
          handler: 'networkFirst',
        },
      ],
      // importScripts: ["./push_message.js"]
    })
  );
});

mix.webpackConfig({
  output: {
    chunkFilename: 'js/[name].[chunkhash].js',
  },
  resolve: {
    alias: {
      '@m': path.resolve(__dirname),
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
});

// For Production
if (mix.inProduction()) {
  let paths = [glob.sync(path.join(__dirname, 'resources/js/**/*.vue')), glob.sync(path.join(__dirname, 'resources/views/**/*.blade.php'))];
  mix.purgeCss({
    content: ['resources/**/*.js', 'resources/**/*.php', 'resources/**/*.vue'],
    whitelistPatterns: [/v-select/, /cv-/, /vs__/, /input/, /flatpickr/, /autocomplete/, /dropdown/],
  });
  mix
    .version()
    .webpackConfig({
      plugins: [
        new CopyWebpackPlugin({ patterns: [{ from: 'resources/assets/images', to: 'images' }] }),
        new ImageminPlugin({
          test: /\.(jpe?g|png|gif|svg)$/i,
          pngquant: { quality: '65-80' },
          plugins: [
            imageminMozjpeg({
              quality: 65,
              progressive: true,
              maxMemory: 1000 * 512,
            }),
          ],
        }),
      ],
    })
    .copy('resources/assets/sw/**/*', 'public')
    .copy('resources/assets/installer/**/*', 'public/installer')
    .serviceWorker();
} else {
  mix.sourceMaps();
}

mix
  .js('resources/js/app.js', 'public/js')
  .sass('resources/sass/app.scss', 'public/css')
  .copy('resources/assets/css/**/*', 'public/css');
// mix.copyDirectory("resources/assets/images", "public/images");
// mix.browserSync("http://sbm.test/");
