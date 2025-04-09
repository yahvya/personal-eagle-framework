# Routing

## Etapes de routing

<tabs>
<tab title="Format procédure">
<procedure title="Première étape">
    <step>Accès à un lien de l'application par l'utilisateur</step>
    <step>redirection via <code>.htaccess</code> sur le point d'entrée <code>sabo-core/index.php</code></step>
    <step>Lancement de l'application via <code>Application::launch</code></step>
    <step>
        <p>Insertion et vérification des configurations dans l'ordre</p>
        <ul>
            <li>fonctions globales</li>
            <li>blade + twig</li>
            <li>environnement</li>
            <li>framework</li>
        </ul>
    </step>
    <step>Initialisation de la base de données si requis</step>
    <step>Lancement du router</step>
</procedure>

<procedure title="Routing">
    <procedure title="Gestion de la maintenance">
        <step>Vérification dans l'environnement</step>
        <step>Si accès déjà autorisé le routing continue</step>
        <step>Sinon affichage de la page de maintenance ou lien spécial d'accès</step>
    </procedure>
    <warning>Si la maintenance laisse le routing se poursuivre uniquement</warning>
    <step>Si le lien est lié à une ressource accessible alors, rendu de la ressource</step>
    <note>Noter qu'en état de maintenance même l'accès aux ressources est bloqué si l'accès n'est pas acquis</note>
    <procedure title="Rendu du site">
        <step>Récupération de la route à afficher</step>
        <step>Vérification des conditions d'accès</step>
        <step>Gestion des données flash</step>
        <step>Appel du controller et de la méthode de gestion</step>
    </procedure>
</procedure>
</tab>

<tab title="Format schéma">
<code-block lang="d2">
# étape d'initialisation
step_one: Première étape{
    user_action: Utilisateur{
        Accès à un lien de l'application{
            shape: text
        }
    }
    ht_access_action: \.htaccess{
        Redirection vers le point d'entrée\n'sabo_core/index\.php'{
            shape: text
        }
    }
    launch_app: class 'Application'{
        Lancement de l'application\nméthode launch{
            shape: text
        }
    }
    insert_and_verify_routing_configs: Insertion et vérification des configurations{
        Dans l'ordre\nfonctions globales, blade + twig, environnement, framework{
            shape: text
        }
    }
    init_elements: Initialisation de la base de données si requis\nLancement du router{
        style.bold: false
    }
    user_action -> ht_access_action -> launch_app -> insert_and_verify_routing_configs -> init_elements
}
# réelle phase de routing
step_two: Routing{
    check_maintenance: Gestion de la maintenance{
        action: Vérification dans l'environnement
        manage_maintenance: Vérification d'accès autorisé
        continue_routing: Continuer le routing
        show_maintenance_page: Affichage de la page de maintenance ou lien spécial d'accès
        manage_maintenance -> continue_routing: Accès déjà autorisé ?
        manage_maintenance -> show_maintenance_page: Non
        action-> manage_maintenance : Oui
    }
    render_resource: Rendu de ressource
    check_maintenance -> render_resource: Vérification de ressource accessible
    check_maintenance -> routing_next: Non
    routing_next: Rendu du site {
        Récupération de la route à afficher -> Vérification des conditions d'accès -> Gestion des données flash -> Appel du controller et de la méthode de gestion
    }
}

step_one -> step_two
</code-block>
</tab>
</tabs>

## Enregistrement des routes

> L'enregistrement des routes passe par le dossier **src/routes**. 

- <code>routes.php</code> fichier utilisé par le framework permettant d'inclure les documents d'enregistrement d'api et web
    <warning>Il n'est pas recommandé de le modifier bien qu'il puisse servir à l'enregistrement des routes</warning>
- <code>web.php</code> sert à l'enregistrement des routes web 
- <code>api.php</code> sert à l'enregistrement des routes d'api

<note>La séparation des deux fichiers n'est que sémantique. Il est recommandé pour les routes d'api de débuter par la création d'un groupe <code>/api</code></note>

<code-block lang="php">
&lt;?php
# routes d'api
use SaboCore\Routing\Routes\RouteManager;
RouteManager::registerGroup(
    linksPrefix: "/api/:apiVersion",
    routes: [],
    genericParamsConfig: ["apiVersion" => "[0-9_]+"],
    groupAccessVerifiers: []
);
# ou
RouteManager::registerGroup(
    linksPrefix: "/api",
    routes: [],
    groupAccessVerifiers: []
);
</code-block>

<warning>Il faut différencier les class <code>RouteManager</code> et <code>Route</code>. La class <code>Route</code> permet de créer une route, la class <code>RouteManager</code> quant à elle sert à la gestion des routes par le framework, mais également à les enregistrer.</warning>

## Enregistrement d'une route

<code-block lang="php">
&lt;?php
// routes web
use SaboCore\Routing\Response\BladeResponse;
use SaboCore\Routing\Routes\Route;
use SaboCore\Routing\Routes\RouteManager;
RouteManager::registerRoute(
    Route::get(
        link: "/",
        toExecute: fn():BladeResponse => new BladeResponse("sabo",["websiteLink" => "https://yahvya.github.io/sabo-final-doc/"]),
        routeName: "sabo"
    )
);
</code-block>

<note>Les méthodes statiques de la class <code>Route</code> représentent les différentes méthodes de requête couramment utilisées. [get, post, put, delete, patch, options, head, trace]</note>

## Enregistrement d'un groupe de routes

<code-block lang="php">
&lt;?php
# routes d'api
use SaboCore\Routing\Routes\RouteManager;
use SaboCore\Routing\Response\BladeResponse;
use SaboCore\Routing\Routes\Route;
RouteManager::registerGroup(
    linksPrefix: "/api",
    routes: [
        Route::get(
            link: "/",
            toExecute: fn():BladeResponse => new BladeResponse("sabo",["websiteLink" => "https://yahvya.github.io/sabo-final-doc/"]),
            routeName: "sabo"
        ),
        Route::get(
            link: "/test",
            toExecute: fn():BladeResponse => new BladeResponse("sabo",["websiteLink" => "https://yahvya.github.io/sabo-final-doc/"]),
            routeName: "sabo"
        )
    ],
    groupAccessVerifiers: []
);
</code-block>

Ce code donnera les routes, accessible en get :

- /api
- /api/test

## Paramètres de création de route

<code-block lang="php">Route::method(string $link, Closure|array $toExecute, string $routeName, array $genericParamsRegex = [], array $accessVerifiers = [])</code-block>

- <code>$link</code> lien associé. Ce lien peut contenir des paramètres génériques au format défini par framework.php. [Voir la définition de format](configuration_elements.md#framework-php)
- <code>$toExecute</code> Callable à appeler pour traiter la requête
  <note>Le callable doit renvoyer une <code>Response</code> et peut se faire injecter un type <code>Request</code> ainsi que les paramètres génériques via leur nom</note>
  
  <code-block lang="php">
    &lt;?php
    # pour la route /test/:username/test2
    function treatmentFunction(Request $requestManager,string $username):JsonResponse{
      return ["username" => $username];
    }
  </code-block>

  ou

  <code>[Controller::class,"methodName"]</code>

- <code>$routeName</code> nom unique de la route *(format libre)*
- <code>$genericParamsRegex</code> configuration des regex associés aux paramètres génériques
  <code-block lang="php">
    &lt;?php
    # pour la route /test/:articleName/:id
    ["articleName" => "[a-z\-0-9]+","id" => "[0-9]+"]
  </code-block>
- <code>$accessVerifiers</code> Conditions de vérification d'accès à la route
  <code-block>
  &lt;?php
  [new Verifier(verifier: [CustomApiController::class,"checkApiAccess"],onFailure: [CustomApiController::class,"refuseApiAccess"])]
  # ou 
  [new Verifier(verifier: fn(Request $requestManager):bool => false,onFailure: fn():RedirectResponse => ...]
  </code-block>
  <note>La classe de vérification permet de définir toute une étape de vérification. La fonction de condition de vérification retournant un booléen, si false la fonction de gestion d'échec qui dans le cas des routes renvoi une <code>Response</code></note>

<note>Les mêmes éléments sont posés sur la création de groupe de route, les conditions sont appliquées à toutes les sous routes.</note>
<warning>Sur un groupe de routes les règles de paramètres génériques sont encapsulés sur le prefix des liens</warning>

## Sous fichiers de route

> Pour apporter une meilleure organisation, des sous fichiers routes peuvent être créés via l'utilisation de <code>RouteManager::fromFile</code>

Cette fonction prend en paramètre le chemin du fichier sans son extension qui doit être ***.php*** avec comme racine le dossier <code>routes</code>.

- routes
  - web.php
  - routes.php
  - api.php
  - custom-routes
    - authentication.php

Pour charger le fichier <code>authentication.php</code>, vous pouvez utiliser la fonction spécifiée ci-dessus dans le fichier <code>web.php</code>.

<code-block>
RouteManager::fromFile(filename: "custom-routes/authentication");
</code-block>

## Mode de développement

> Lors de la phase de routing le mode de développement défini si oui ou non la récupération d'exception non gérée est capturée ou non. Si dev mode à <code>true</code> alors les erreurs ne sont pas capturées sinon elles le sont et la page d'erreur interne est affichée.

[Mise à jour du mode dans l'environnement](configuration_elements.md#env-php)