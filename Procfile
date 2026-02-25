web: php -d variables_order=EGPCS artisan serve --host=0.0.0.0 --port=${PORT:-8000}
release: php artisan migrate --force && php artisan cache:clear
worker: php artisan queue:work --sleep=3 --tries=3 --timeout=90
