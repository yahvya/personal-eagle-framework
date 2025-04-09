# Accès aux routes

> L'accès aux routes dans l'application passe par des fonctions définies globalement, accessible via le programme, blade mais aussi le passage de routes nommées à Javascript.

<note>L'implémentation pour twig peut se faire en définissant une nouvelle extension pouvant être enregistré dans la configuration en copiant l'équivalent blade.</note>

## Route nommée
<code>route</code> fonction permettant de récupérer le lien d'une route nommée

<code-block lang="php">
route(requestMethod: "get ou post ou put ...",routeName: "nom_de_la_route",replaces: ["generic1" => 10]);
</code-block>

## Passage de route à javascript

- <code>bladeJsRoutes</code> pour blade.

<note>Référez-vous aux commentaires des fonctions.</note>