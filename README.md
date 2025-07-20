# Kanboard-App – Guide d’installation et de lancement en local

Ce guide décrit toutes les étapes pour cloner, configurer et lancer le projet Laravel/Sail en local.

---

## Les prérequis pour lancercle projet

- Docker Desktop (macOS/Windows) ou Docker Engine + Docker Compose (Linux)
- Git
- Au moins 1 Go de RAM alloué à Docker
- Un éditeur de code

---

## 1. Cloner le dépôt

```bash
cd  ## Dans le dossier de votre choix
git clone git@github.com:Faalco91/Kanboard-Project.git
cd /kanboard-app
```

## 2. Configurer l’environnement

- Remplacer le .env.example par .env
- ouvrir le .env et intégrer le contenu suivant dans le fichier: 

```bash
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:eK95D1NFUV+gPSi1+UBXo6hyibaMQMGd5NaN16JroNo=
APP_DEBUG=true
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
# CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=resend
RESEND_API_KEY=re_GsDDhY5A_5G7eyvvM7CTw9FG3jc5gTbUP
MAIL_FROM_ADDRESS=no-reply@kanboard.guru
MAIL_FROM_NAME="Kanboard"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```


## 3. Installer les dépendances PHP

- Installer les dépendances PHP via Composer:
```bash
composer install
```


## 4. Lancer toute la stack

- Pour lancer le serveur docker avec sail:
```bash
./vendor/bin/sail up -d
```


## 5. Initialiser la base de données

- Exécuter les migrations
``` bash
./vendor/bin/sail artisan migrate
```

## 6. Installer et compiler les assets front-end

- Pour installer les dépendances:
```bash
./vendor/bin/sail npm install
```

- Pour lancer en mode développement:
```bash
./vendor/bin/sail npm run dev
```

- Pour lancer en mode de production:
```bash
./vendor/bin/sail npm run build
```


## Accéder à l'application

Bravo, l'application doit être normalement bien accéssible à l'adresse suivante http://localhost/


## Bonus. Quelques commandes utiles

```bash
./vendor/bin/sail up -d --build	#Rebuild & démarre tous les conteneurs
./vendor/bin/sail down	#Arrête et supprime les conteneurs
./vendor/bin/sail restart	#Redémarre les conteneurs actifs
./vendor/bin/sail artisan cache:clear	#Vide les caches Laravel
./vendor/bin/sail shell	#Ouvre un shell dans le conteneur Laravel
```