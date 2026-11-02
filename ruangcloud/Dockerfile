# ── Tahap 1: build aset frontend (Vite) ──────────────────────────────
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# ── Tahap 2: aplikasi Laravel di PHP-FPM ─────────────────────────────
FROM php:8.2-fpm-bookworm AS app
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      git unzip libzip-dev libonig-dev libxml2-dev libicu-dev default-mysql-client \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring xml bcmath zip exif pcntl intl \
 && apt-get clean && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php.ini /usr/local/etc/php/conf.d/ruangcloud.ini
WORKDIR /var/www/html
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod +x docker/entrypoint.sh
ENTRYPOINT ["docker/entrypoint.sh"]
CMD ["php-fpm"]

# ── Tahap 3: web server Nginx (statis + teruskan PHP ke app) ─────────
FROM nginx:alpine AS web
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=app /var/www/html/public /var/www/html/public
