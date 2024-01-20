# OpenClassrooms - Projet 8 : Améliorez une application existante de ToDo & Co

## Présentation

Dépôt Git de [todolist](https://github.com/jeremymls/todolist).

Ce projet est le huitième projet de la formation Développeur d'application - PHP/Symfony d'OpenClassrooms.

Voir le projet sur le site d'OpenClassrooms :
[https://openclassrooms.com/projects/ameliorer-un-projet-existant-1](https://openclassrooms.com/projects/ameliorer-un-projet-existant-1)

## Configuration conseillée

Le projet a été développé sur un serveur local avec les versions suivantes :

> - Apache 2.4.51
> - PHP 8.1.0
> - [MySQL](https://www.mysql.com/fr/) 5.7.36
> - [Composer](https://getcomposer.org/) 2.6.6
> - [Node.js](https://nodejs.org/en/) 18.17.1
> - [Yarn](https://yarnpkg.com/) 1.22.19

## Installation

- Cloner le dépôt Git

```bash
git clone git@github.com:jeremymls/todolist.git
```

- Dans le dossier cloné (`todolist`), copier le fichier **.env** et le renommer en **.env.local**

```bash
cd todolist
cp .env .env.local
```

- Configurer les variables d'environnement dans le fichier **.env.local**

- Lancer le script d'installation

```bash
sh deploy.sh
```

## Assigner les tâches existantes à un utilisateur inconnu

- Lancer la commande suivante :

```bash
php bin/console todolist:assign_tasks_to_unknown_user
```
