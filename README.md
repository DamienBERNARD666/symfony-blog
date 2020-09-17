# Installation

Importer le projet dans votre github

`git clone ...`

En local, modifier la variable `DATABASE_URL` de votre fichier `.env`.

Lancer la commande
`composer install`

Créer la base de donnée
`php bin/console doctrine:database:create`

Récupérer la structure de la base de donnée
`php bin/console doctrine:migrations:migrate`

Lancer le serveur
`php bin/console server:run` ou `symfony server:start`

La page qui affichera le blog est disponible à l'url:  http://127.0.0.1:8000