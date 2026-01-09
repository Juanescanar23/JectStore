# syntax=docker/dockerfile:1

FROM node:20-alpine AS node-build
WORKDIR /app
ENV PLAYWRIGHT_SKIP_BROWSER_DOWNLOAD=1

COPY package*.json vite.config.* ./
COPY resources/ resources/
RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi
RUN mkdir -p public
RUN npm run build

COPY packages/Webkul/Admin packages/Webkul/Admin
RUN cd packages/Webkul/Admin \
    && npm install --no-audit --no-fund \
    && npm run build

COPY packages/Webkul/Shop packages/Webkul/Shop
RUN cd packages/Webkul/Shop \
    && npm install --no-audit --no-fund \
    && npm run build

COPY packages/Webkul/Installer packages/Webkul/Installer
RUN cd packages/Webkul/Installer \
    && npm install --no-audit --no-fund \
    && npm run build

FROM php:8.2-cli-bookworm AS app
WORKDIR /app

RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    ca-certificates \
    git \
    unzip \
    libcurl4-openssl-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libpng-dev \
    libzip-dev \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        calendar \
        curl \
        gd \
        intl \
        mbstring \
        pdo_mysql \
        zip

RUN pecl install redis \
    && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . .
COPY --from=node-build /app/public/build /app/public/build
COPY --from=node-build /app/public/themes/admin/default/build /app/public/themes/admin/default/build
COPY --from=node-build /app/public/themes/shop/default/build /app/public/themes/shop/default/build
COPY --from=node-build /app/public/themes/installer/default/build /app/public/themes/installer/default/build

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p public/fonts \
    && curl -fsSL -o public/fonts/NotoSansSC-Regular.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansSC/NotoSansSC-Regular.ttf \
    && curl -fsSL -o public/fonts/NotoSansSC-Bold.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansSC/NotoSansSC-Bold.ttf \
    && curl -fsSL -o public/fonts/NotoSansJP-Regular.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansJP/NotoSansJP-Regular.ttf \
    && curl -fsSL -o public/fonts/NotoSansJP-Bold.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansJP/NotoSansJP-Bold.ttf \
    && curl -fsSL -o public/fonts/NotoSansBengali-Regular.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansBengali/NotoSansBengali-Regular.ttf \
    && curl -fsSL -o public/fonts/NotoSansBengali-Bold.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansBengali/NotoSansBengali-Bold.ttf \
    && curl -fsSL -o public/fonts/NotoSansSinhala-Regular.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansSinhala/NotoSansSinhala-Regular.ttf \
    && curl -fsSL -o public/fonts/NotoSansSinhala-Bold.ttf https://github.com/googlefonts/noto-fonts/raw/main/hinted/ttf/NotoSansSinhala/NotoSansSinhala-Bold.ttf

CMD ["sh", "-c", "php artisan migrate --force && php -S 0.0.0.0:${PORT:-8080} -t public"]
