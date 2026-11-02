#!/bin/sh
# Entrypoint container aplikasi RuangCloud.
# - Menunggu database siap.
# - Khusus service "app" (RUN_MIGRATIONS=1): migrasi + storage:link + seed awal.
set -e

echo "Menunggu database di ${DB_HOST:-db}:${DB_PORT:-3306}..."
until php -r '
    try {
        new PDO(
            "mysql:host=" . (getenv("DB_HOST") ?: "db") . ";port=" . (getenv("DB_PORT") ?: "3306"),
            getenv("DB_USERNAME"),
            getenv("DB_PASSWORD")
        );
        exit(0);
    } catch (Throwable $e) {
        exit(1);
    }
' 2>/dev/null; do
    sleep 2
done
echo "Database terhubung."

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
    mkdir -p storage/app/public storage/app/private storage/app/nodes
    php artisan migrate --force
    php artisan storage:link || true
    if [ "${RUN_SEED:-0}" = "1" ]; then
        php artisan db:seed --class=RuangCloudSeeder --force
    fi
    chown -R www-data:www-data storage bootstrap/cache || true
fi

exec "$@"
