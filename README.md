step follow for setup
git clone <remote-url>

please create .env file and set database name

composer install

php artisan config:cache

if import data direct sql file then no need to run
php artisan migrate
php artisan db:seed
php artisan passport:install


