# Apis

> <code>SaboApi</code> du namespace <code>SaboCore\Utils\Api</code> est l'utilitaire d'appel d'api du framework. 

## Fonctions offertes

- <code>constructeur</code> le constructeur prend en paramètre un préfixe. Ce préfixe est stocké afin de pouvoir être utilisé pour chaque appel à la méthode <code>apiUrl</code>
- <code>apiUrl</code> fourni la concaténation du préfixe d'api enregistré avec la suite de lien fourni
- <code>request</code> permet de lancer une requête vers l'api à partir des données fournies. *Référez-vous aux commentaires pour plus de détails sur les paramètres*
    <warning>L'URL demandée est une url complète (pouvant être obtenue via <code>apiUrl</code>)</warning>
- <code>getLastRequestResult</code> fourni le résultat de la dernière requête lancée accessible sous 2 formats
    - tableau via <code>SaboApiRequest::RESULT_AS_JSON_ARRAY</code>
    - chaine de caractères via <code>SaboApiRequest::RESULT_AS_STRING</code> *cette version renvoi le résultat original de la requête*

- <code>ifArrayContain</code> cette méthode est un utilitaire permettant notamment à la suite d'une récupération au format <code>SaboApiRequest::RESULT_AS_JSON_ARRAY</code> de vérifier la présence des clés attendues *Référez-vous aux commentaires pour plus de détails sur le format*

- <code>createFromConfig</code> cette méthode statique fournie une instance de SaboApi à partir de la configuration fournie

## Utilisation de la class

La class est abstraite de base, l'idée dans son implémentation est de créer une class par Api utilisée.

<code-block lang="php">
class SaboOnlineApi extends SaboApi{
    public function __construct(){
        parent::construct(apiUrlPrefix: "https://sabo-false-url.com/api");
    }
}
</code-block>

<warning>L'utilisation de la méthode statique <code>createFromConfig</code> permet de créer une instance utilisable, mais il est recommandé pour une structure propre de créer une class autour de l'api utilisé</warning>

<note>Actuellement l'implémentation de cet utilitaire se base autour des fonctions natives de <code>curl</code>, il est prévu de l'orienter autour du package <code>Http</code></note>