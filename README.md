# Symfony Film API

Ce web service permet D'authentifier des utilisateurs

# Installation

## Installation de l'application
```
git clone https://github.com/raphmath2002/symfony_auth_api
cd symfony_auth_api
composer install
```

## Installation de la base de données et population

 1. Complétez la variable d'environnement "DATABASE_URL"

```
php bin/console app:migrate
```

## Lancement

```
symfony server:start
```
