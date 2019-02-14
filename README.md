## Intallation

Comment installer le projet : 

- Lancer docker : `docker-compose up -d`
- Accéder au container nginx : `docker-compose exec web /bin/bash`
- Installer les dépendances avec composer : `composer install`
- Modifier le .env :
```bash
DATABASE_URL=mysql://root:root@database/projet1
MAILER_URL=smtp://mailhog:1025
```
- Installer la bdd (si non créée) : `php bin/console doctrine:database:create`
- Update la bdd : `php bin/console d:s:u --force`
- Générer les fixtures : `php bin/console hautelook:fixtures:load`


## Run 

Pour accéder au projet : 

```bash
localhost:8000
```

## Technologie

Technologies utilisées : 

- Symfony 4
- Javascript/JQuery
- Ajax
- CSS
- PHP

## Description

Le projet consiste à faire voter en ligne des conférences par les utilisateurs possédant un compte sur la plateforme.

##### Un visiteur peut : 

- Accéder à la home et voir toutes les conférences en ligne
- Voir toutes les conférences notées et celles non notées
- Créer un compte

##### Un utilisateur peut accéder à son dashboard et ainsi :

- Voir les conférences qu'il a noté
- Modifier ses données
- Voter une conférence de 1 à 5

##### Un admin peut accéder à son dashboard et ainsi : 

- Voir le top 10 des conférences votées
- Voir les derniers visiteurs qui ont créer un compte
- Voir la liste des utilisateurs et les modifier ou supprimer
- Voir la liste des conférences et les modifier ou supprimer
- Créer une conférence (avec un envoie de mail aux utilisateurs)
- Créer un utilisateur
- Voir les conférences qu'il a noté
- Modifier ses données