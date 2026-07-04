# syntax=docker/dockerfile:1
# Warehaus — Laravel 13 + PHP 8.4 image for Railway.
# Railway's default Nixpacks provider was pinning PHP 8.3 and omitting ext-gd,
# breaking composer install for Laravel 13 (needs 8.4+) and phpspreadsheet (needs gd).

FROM php:8.4-cli-alpine

# System deps + PHP extensions
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
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        gd \
        zip \
        bcmath

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

# Generate optimized autoloader, register Laravel packages, build frontend,
# and link storage. storage:link may error if the symlink already exists — safe to ignore.
RUN composer dump-autoload --optimize --classmap-authoritative --no-scripts \
    && php artisan package:discover --no-interaction \
    && npm run build \
    && (php artisan storage:link || true)

ENV PORT=8080
EXPOSE 8080

# Cache config/routes/views at runtime so env vars from Railway are baked in,
# then serve via the built-in PHP server bound to 0.0.0.0.
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}"]
