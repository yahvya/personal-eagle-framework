# Framework Sabo

> Sabo est un framework php visant à faciliter le développement d'applications web tout en conservant le plaisir d'écrire du PHP.

*Le site de documentation du framework est en cours de création*

## Gestion du projet

### Créer un projet

```
mkdir <mondossierprojet>
cd <mondossierprojet>
git clone https://github.com/yahvya/sabo-final.git .
cd sabo-core
composer install
cd ..
composer install
rm README.md
cd src/views/mails
rm .gitkeep
cd ../../configs
mv env.example.php env.php
cd ../storage/maintenance
mv maintenance.secret.example maintenance.secret
cd ../../../
rm -r -fo .git
rm -r .git
clear
php sabo serve
```

### Lancement du site

> Vous pouvez utiliser un hôte virtuel (ex : généré par laragon) ou le serveur de développement intégré en utilisant la commande suivante

```
php sabo serve
```

## Structure

> Le dossier src contient les éléments servant à la création de l'application

- Le dossier **configs** contient les fichiers de configurations (environnement, framework, configuration blade / twig ainsi que les fonctions globales à l'application)
- Le dossier **controllers** vise à accueillir les controllers, par défaut la class abstraite CustomController y est ajoutée afin de servir de classe mère customisable aux futurs controllers ainsi que le controller d'accès à la maintenance par défaut
- Le dossier **models** vise à accueillir les models, par défaut la class abstraite CustomModel y est ajoutée afin de servir de classe mère customisable aux futurs models
- Le dossier **public** visant à accueillir toutes les ressources publiques
- Le dossier **storage** représente le dossier de stockage de l'application, par défaut contient le fichier hash d'accès à la maintenance
- Le dossier **routes** contenant les différentes routes de l'application
- Le dossier **treatment** visant à contenir les class de traitement contient par défaut CustomTreatment classe parent customisable des class de traitement
- Le dossier **views** racine de recherche des éléments de vue, peut contenir du css et js accessible  