#!/bin/sh
cd ./vendor/laravel/nova
mv webpack.mix.js.dist webpack.mix.js
npm i
npm run dev
rm -rf node_modules
cd -
php artisan nova:publish
