# Kanboard-App – Guide d’installation et de lancement en local

Ce guide décrit toutes les étapes pour cloner, configurer et lancer le projet Kanboard en local.

---

## Les prérequis pour lancer le projet

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