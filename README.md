## Dealabs - BOUGRAT Alexandre - JARRY Allan

### Installation

Sur git la version la plus stable est sur la branche `master` avec le tag `1.0.0`

https://gitlab.iut-clermont.uca.fr/php-symfony/promo-2021/dealabs/jarry-bougrat/-/tree/master

Le projet n'étant pas sous une stack docker, une solution tel que WAMP est nécessaire.

AUSSI, afin de lancer le serveur, il est nécessaire d'avoir le CLI symfony d'installer, sinon la commande de l'étape 7 ci-dessous ne fonctionnera pas.

Les étapes à suivre afin de mettre en place le projet :

1. `composer install`

2. `yarn install`

3. `symfony console doctrine:database:create` ou `php bin/console doctrine:database:create` (De base la création va utiliser l'identifiant "root" sans mot de passe et créer une BDD nommé "dealabs")

4. `symfony console d:m:m` ou `php bin/console doctrine:migrations:migrate`

5. Pour ajouter des fixtures `symfony console hautelook:fixtures:load` ou `php bin/console hautelook:fixtures:load`

6. Load js routes `php bin/console assets:install --symlink public`

7. Generate json routes for JS `php bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json`

8. `yarn watch`

9. `symfony serve`
