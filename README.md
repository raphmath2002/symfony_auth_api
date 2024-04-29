# Symfony Film API

Ce web service permet d'effectuer un CRUD sur des films, lien avec des catégories et destion de leur affiche


# Installation

## Installation de l'application
```
git clone https://github.com/raphmath2002/symfony_film_api_v2
cd symfony_film_api
composer install
```

## Installation de la base de données et population

 1. Exécutez les fichiers de migration qui se trouvent dans le dossier "migrations"
 2. Complétez la variable d'environnement "DATABASE_URL"

```
php bin/console doctrine:fixtures:load
```

## Lancement

```
symfony server:start
```
