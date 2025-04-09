# Vérifications 

> L'utilitaire de vérification, utilisé notamment pour définir les conditions sur les routes, permet la définition d'un processus de vérification.

<procedure title="Processus de vérification">
<step>Le constructeur prend trois paramètres <code>verifier</code> la fonction de vérifiant retournant un booléen de succès. Deux paramètres optionnels <code>onFailure</code> callable de gestion d'échec et <code>onSuccess</code> callable de gestion de succès.</step>
<step>Appel de la méthode <code>verify</code> pour lancer la fonction de vérification ou lancer le processus complet via <code>execVerification</code> en fournissant les arguments à envoyer à chacun des callables fournis en constructeur.</step>
</procedure>

## Utilisation

<code-block lang="php">
$loginAccessVerifier = new Verifier(
    verifier: fn(bool $toReturn):bool => $toReturn,
    onSuccess: function(string $sentence):void{ echo $sentence; }
);
$loginAccessVerifier->execVerification(verifierArgs: [true],onSuccessArgs: ["sabo framework"]);
</code-block>