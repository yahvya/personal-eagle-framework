# Les class Request et Response

> Les objets <code>Request</code> et <code>Response</code> sont respectivement un objet permettant de récupérer des informations sur la requête et d'interagir avec la session ainsi qu'un objet permettant de rendre une réponse <code>HTTP</code>.

## L'objet Request

Il existe deux manières de récupérer une instance de cet objet.

La première est de l'injecter via les paramètres de la méthode d'un <code>callable</code> gestionnaire de requête (ex: la méthode d'un controller) en ajoutant le type Request sur le paramètre.

La deuxième est simplement de créer une nouvelle instance la class, ***toutefois mieux vaux utiliser la première méthode pour éviter des duplicatas d'instance.***

<code-block lang="php">
function showHomePage(Request $myRequest):BladeResponse{
    return ...;
}
</code-block>

<code-block lang="php">
class MyController extends CustomController{
    public function showHomePage(Request $myRequest):BladeResponse{
        return ...;
    }
}
</code-block>

Elle propose quelques méthodes utilitaires telles que :

- <code>getSessionStorage</code> fourni l'instance interne de <code>SessionStorage</code> l'utilitaire d'interaction avec la session
- <code>getPostValues</code> fourni un tableau des valeurs <code>POST</code> demandées. Dans le cas où une des clés requises n'est pas trouvée
    - si le message d'erreur n'est pas <code>NULL</code> alors une <code>TreatmentException</code> est levée
    - sinon null est retournée
- <code>getGetValues</code> même principe que <code>getPostValues</code> sur les valeurs <code>GET</code>
- <code>getCookieValues</code> même principe que <code>getPostValues</code> sur les valeurs <code>COOKIES</code>
- <code>getFilesValues</code> même principe que <code>getPostValues</code> sur les valeurs <code>FILES</code>
- <code>getMethod</code> fourni la méthode de requête utilisée formatée en minuscule (GET, POST, PUT ...)
- <code>getValuesFrom</code> utilitaire de récupération de donnée à partir d'un conteneur sous forme de tableau

## L'objet Response

> L'objet <code>Response</code> est une class abstraite implémentant par défaut la logique de rendu d'une réponse <code>HTTP</code> le framework implémente certains modèles de réponse par défaut

- <code>BladeResponse</code> pour rendre un visuel à partir d'un template blade
- <code>TwigResponse</code> pour rendre un visuel à partir d'un template twig
- <code>HtmlResponse</code> pour rendre un visuel à partir d'un contenu html (utilisé par blade et twig)
- <code>DownloadResponse</code> pour rendre une ressource à télécharger
- <code>JsonResponse</code> pour rendre du contenu <code>JSON</code>
- <code>RedirectResponse</code> pour rediriger sur un lien fourni
- <code>ResourceResponse</code> pour rendre une ressource *utilisé par le framework pour rendre les ressources*

### Procédure de rendu d'une réponse

<tabs>
<tab title="Procédure">
<procedure>
<step>Envoi du code retour</step>
<step>Envoi des en-têtes de réponse</step>
<step>Rendu du contenu</step>
</procedure>
</tab>
<tab title="Schéma">
<code-block lang="d2">
Envoi du code retour -> Envoi des en-têtes de réponse -> Rendu du contenu
</code-block>
</tab>
</tabs>

Liste des en-têtes par défaut défini par le framework :

- <code>X-Content-Type-Options</code> à <code>nosniff</code>
- <code>Cache-Control</code> à <code>no-cache, no-store, must-revalidate</code>
- <code>Strict-Transport-Security</code> à <code>max-age=31536000; includeSubDomains</code>

> Des fonctions permettent toutefois de mettre à jour le code de retour ainsi que de modifier les en-têtes

- <code>setHeader</code> permet de mettre à jour un en-tête précis
- <code>setContent</code> permet de mettre à jour le contenu textuel à rendre
- <code>setResponseCode</code> permet de mettre à jour le code de retour <code>HTTP</code>
  
    Cette fonction utilise l'énumération <code>SaboCore\Routing\Response\ResponseCode</code>

<warning>Il n'est pas recommandé de mettre à jour manuellement le rendu textuel</warning>