# Csrf

> La protection <csrf <code>Cross Site Request Forgery</code> permet de protéger un site de requête non défini par elle. [Plus d'informations](https://fr.wikipedia.org/wiki/Cross-site_request_forgery)

La méthode de protection commune contre ce genre d'attaque passe par l'utilisation d'un token re-généré pour les requêtes.

Afin de récupérer un token la fonction <code>generateCsrf()</code> est définie de manière globale dans <code>src/config/functions.php</code>. 

Cette fonction est donc accessible dans le code ainsi que dans les templates de rendu <code>blade</code> et <code>twig</code>.

## Utilisation de la fonction

La fonction retourne un objet <code>CsrfManager</code> dont la méthode <code>getToken</code> fourni le token généré.

<code-block lang="twig">
{# exemple twig #}    
{{ generateCsrf().getToken() }
</code-block>

<code-block lang="blade">
{-- exemple blade --}
{{ generateCsrf()->getToken() }}
</code-block>

<code-block lang="php">
# exemple dans le code php
generateCsrf()->getToken()
</code-block>

## Vérification du token

Afin de vérifier un token, la seconde fonction <code>checkCsrf</code> est définie de manière globale dans <code>src/config/functions.php</code>

Cette fonction prend en paramètre une chaine supposée <code>token</token> et retourne si le token généré est valide.