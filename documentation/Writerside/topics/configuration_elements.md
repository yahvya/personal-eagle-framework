# Les éléments de configuration

> Le dossier **src/configs** contient différents fichiers de configurations, permettant de gérer du framework, aux fonctions globales de l'application.


## [functions.php]

<note>Ce fichier contient l'ensemble des fonctions globales à l'application. Une fonction qui y est définie est accessible à tout endroit du code.</note>

*La liste suivante peut changer en fonction des modifications rapides apportées*

### Liste des fonctions actuellement définies

- <code>debug</code> fonction à paramètres multiples, permet de debugger les variables fournies
- <code>debugDie</code> fonction à paramètres multiples, permet de debugger les variables fournies puis stoppe l'exécution <code>die</code>
- <code>route</code> fonction permettant de récupérer le lien d'une route nommée
  <code-block lang="php">
    route(requestMethod: "get ou post ou put ...",routeName: "nom_de_la_route",replaces: ["generic1" => 10]);
  </code-block>
- <code>generateCsrf</code> génère un code csrf et fourni le gestionnaire créé
    <code-block lang="php">
        generateCsrf()->getToken();
    </code-block>
- <code>checkCsrf</code> vérifie un token csrf et retourne son état de validité
    <code-block lang="php">
        if(!checkCsrf(token: $_POST["token"]))
            throw new \Exception(message: "Token invalide");
    </code-block>

## [framework.php]

<note>Ce fichier contient les configurations modulables du framework (chemins, formats ...). Il permet également d'ajouter des configurations qui seront chargées globalement dans l'application.</note>

- Définition du timezone par défaut
- Définition du chemin du dossier des ressources publiques
- Définition du chemin du dossier de stockage
- Définition du chemin du dossier des routes
- Définition de la liste des extensions autorisées à l'accès publique
- Définition de la regex permettant la définition du format d'un paramètre générique. (<code>`{generic_url_param}`</code> ou <code>:generic_url_param</code> ou <code>format_personnalisé</code>)
  
    > Cette regex doit permettre de capturer le nom du paramètre générique tout en reconnaissant les symboles liés

    ex: <code>`{generic_url_param}`</code> = <code>"\{([a-zA-Z]+)\}"</code> Le nom du paramètre est capturé et les accolades détectées.

## [twig-config.php]

<note>Ce fichier contient la fonction d'enregistrement des extensions twig.</note>

## [blade-config.php]

<note>Ce fichier contient la fonction d'enregistrement des directives blade ainsi que la fonction de récupération de route formatée pour javascript.</note>

**Référez-vous aux commentaires des fonctions**

## [env.php]

<note>Configuration d'environnement</note>

