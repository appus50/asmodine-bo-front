# Lancement rapide :

## Installation

Récupérer les sources :

```bash
git clone http://80.12.81.32:8089/appus/asmodine.front.git
```

>>>
Si vous utilisez Docker avec le [projet associé](http://80.12.81.32:8089/appus/asmodine.docker),
connectez-vous avec la commande suivante une fois le projet *asmodine.docker* configuré et lancé :

```bash
docker exec -it asmodine_front_php /bin/bash
```
Puis, continuer normalement.
>>>

Générer les clefs nécessaire à l'import de *asmodine.common* : [Voir README de asmodine.common](http://80.12.81.32:8089/appus/asmodine.common/blob/master/README.md)

Installer les dépendances :

```bash
composer install
```

Générer une clef JWT pour les sessions qui seront en stateless :

```
openssl genrsa -out var/jwt/private.pem -aes256 4096
openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem
```

## Initialisation de la base

Lancer les commandes suivantes :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console assetic:dump --env=prod
```

Ces commandes réalisent les actions suivantes :
 - Création de la BDD (Inutile avec Docker => La création est automatique)
 - Création des tables
 - Publication des assets *(supprimer --env=prod pour le développement)*
 
## Ajout des vidéos

Ajouter les vidéos suivantes dans le répertoire **/web/videos/** :
 - concept/:
    - lifestyle.mp4
 - measure/:
    - arm_length_men.mp4
    - arm_length_women.mp4
    - breast_women.mp4
    - chest_men.mp4
    - chest_women.mp4
    - foot_length_men.mp4
    - foot_length_women.mp4
    - foot_width_men.mp4
    - foot_width_women.mp4
    - hip_men.mp4
    - hip_women.mp4
    - inside_leg_men.mp4
    - inside_leg_women.mp4
    - leg_length_men.mp4
    - neck_men.mp4
    - neck_women.mp4
    - shoulder_men.mp4
    - shoulder_women.mp4
    - thigh_men.mp4
    - thigh_women.mp4
    - waist_men.mp4
    - waist_women.mp4

Vous pouvez ajouter d'autre vidéos en respectant la règle suivante : partieducorps_(men|women).mp4

Les différentes parties du corps sont définies dans le fichier *Body.php* du projet *Asmodine Common*.