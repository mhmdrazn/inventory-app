# syntax=docker/dockerfile:1
# Warehaus — Laravel 13 + FrankenPHP (Caddy-based server) for Railway.
#
# Previously used `php artisan serve` which is single-threaded and queues
# every concurrent request — one slow image download would block page
# navigation. FrankenPHP handles requests concurrently via Caddy, so
# page-to-page navigation is no longer blocked by asset requests.

FROM dunglas/frankenphp:1-php8.4-alpine

# System deps + PHP extensions
# install-php-extensions is a helper shipped in the FrankenPHP image.
RUN apk add --no-cache \
        postgresql-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        oniguruma-dev \
        nodejs \
        npm \
        git \
        unzip \
        bash \
    && install-php-extensions \
        pdo_pgsql \
        gd \
        zip \
        bcmath \
        opcache \
        intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# --- Dependency layer (cached when manifests unchanged) ---
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --prefer-dist \
        --no-progress \
        --no-scripts \
        --no-autoloader \
        --no-interaction

COPY package.json package-lock.json .npmrc ./
RUN npm ci

# --- App layer ---
COPY . .

RUN composer dump-autoload --optimize --classmap-authoritative --no-scripts \
    && php artisan package:discover --no-interaction \
    && npm run build \
    && (php artisan storage:link || true)

# Laravel needs write access to these dirs at runtime.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Railway injects $PORT. FrankenPHP's default Caddyfile reads SERVER_NAME,
# where ":<port>" means bind on all interfaces at that port.
ENV PORT=8080
EXPOSE 8080

# Cache config/routes/views at runtime (Railway env vars are now available),
# then exec into FrankenPHP so SIGTERM propagates for graceful shutdown.
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && export SERVER_NAME=\":${PORT:-8080}\" && exec frankenphp run --config /etc/caddy/Caddyfile"]
