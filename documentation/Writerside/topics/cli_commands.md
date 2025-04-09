# Commandes cli

> Le framework vient avec un petit utilitaire en ligne de commandes visant à automatiser certaines actions. L'utilisateur peut ajouter ses propres commandes.

## Utilisation

L'utilitaire se base sur un fichier PHP sans extension <code>sabo</code>. 

Il peut donc être lancé via la commande PHP <code>php sabo nom_de_la_commande [options de la commande]</code>

## Liste des commandes par défaut

- <code>help</code> gestionnaire d'aide
- <code>serve</code> lance le serveur de développement
    <note>Il est intéressant de vérifier les options de cette commande. L'option utilisant <code>browser-sync</code> lance le serveur interactif utile pour le front end.</note>
- <code>make:controller</code> création de controller
- <code>make:model</code> création de model

## Créer une commande

<procedure>
<step>Définir une class qui extends <code>SaboCore\Cli\Commands\SaboCommand</code></step>
<step>Enregistrer la commande dans le fichier sabo en suivant le même model que les commandes précédentes</step>
</procedure>