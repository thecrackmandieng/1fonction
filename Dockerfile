# Étape 1 : Image de base optimisée avec PHP 8.3 et Apache
FROM php:8.3-apache

# Définir les variables d'environnement
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    mariadb-client \
    && docker-php-ext-configure gd \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        zip \
        gd \
        mbstring \
        exif \
        pcntl \
        bcmath \
        opcache

# Configurer Apache pour Laravel
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Activer les modules Apache nécessaires
RUN a2enmod rewrite headers

# Copier Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de configuration d'abord pour optimiser le cache Docker
COPY composer.json composer.lock ./

# Installer les dépendances Composer
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copier le reste du projet
COPY . .

# Copier le fichier .env.example si .env n'existe pas
RUN if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi

# Définir les permissions correctes pour Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Générer la clé d'application Laravel
RUN php artisan key:generate

# Optimiser Laravel pour la production
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Configurer OPcache pour les performances
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Nettoyer le cache APT pour réduire la taille de l'image
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Exposer le port 80
EXPOSE 80

# Commande par défaut
CMD ["apache2-foreground"]
