# Docker Setup Guide - Laravel API

Ce guide vous explique comment utiliser Docker pour exécuter l'API Laravel avec la base de données MySQL.

## Prérequis

- Docker et Docker Compose installés
- Ports disponibles: 8000 (app), 3306 (MySQL), 8080 (phpMyAdmin)

## Démarrage rapide

### 1. Construction et lancement

```bash
# Construire et lancer tous les services
docker-compose up -d

# Vérifier que les services sont en cours d'exécution
docker-compose ps
```

### 2. Accès aux services

- **API Laravel**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **API Endpoints**: http://localhost:8000/api

### 3. Configuration de la base de données

```bash
# Exécuter les migrations
docker-compose exec app php artisan migrate

# Remplir la base de données avec des données de test
docker-compose exec app php artisan db:seed
```

## Commandes utiles

### Gestion des conteneurs

```bash
# Arrêter tous les services
docker-compose down

# Reconstruire l'image sans cache
docker-compose build --no-cache

# Voir les logs
docker-compose logs -f app
docker-compose logs -f db
```

### Accès au conteneur

```bash
# Accéder au conteneur Laravel
docker-compose exec app bash

# Exécuter des commandes Artisan
docker-compose exec app php artisan route:list
docker-compose exec app php artisan tinker
```

### Tests

```bash
# Exécuter les tests
docker-compose exec app php artisan test

# Tests avec couverture
docker-compose exec app php artisan test --coverage
```

## Structure des services

- **app**: Conteneur Laravel avec Apache
- **db**: Base de données MySQL 8.0
- **phpmyadmin**: Interface web pour gérer MySQL

## Configuration

### Variables d'environnement

Les variables sont définies dans `docker-compose.yml` :
- APP_ENV=local
- APP_DEBUG=true
- DB_HOST=db
- DB_DATABASE=laravel_api
- DB_USERNAME=laravel
- DB_PASSWORD=secret

### Base de données

- **Host**: db (nom du service)
- **Port**: 3306
- **Database**: laravel_api
- **User**: laravel
- **Password**: secret

## Production

Pour la production, modifiez les variables d'environnement dans le fichier `.env` :

```bash
APP_ENV=production
APP_DEBUG=false
```

Et utilisez la commande :

```bash
docker-compose -f docker-compose.yml up -d
```

## Dépannage

### Problèmes courants

1. **Port déjà utilisé**: Modifiez les ports dans `docker-compose.yml`
2. **Permissions**: Assurez-vous que les dossiers storage et bootstrap/cache ont les bonnes permissions
3. **Base de données**: Vérifiez que MySQL est bien démarré avant de lancer les migrations

### Nettoyer Docker

```bash
# Supprimer les conteneurs et volumes
docker-compose down -v

# Supprimer les images
docker image prune -a
