# Chaine de caractères

## Générateur de chaine aléatoire

La class abstraite <code>SaboCore\Utils\String\RandomStringGenerator</code> fourni via la méthode <code>generateString</code> une manière de récupérer une chaine aléatoire toutefois formatée

- <code>length</code> longueur de la chaine attendue
- <code>removeSimilarChars</code> supprime les caractères similaires lors de la génération (i I L l par exemple)
    <note>Celà peut être utile pour des codes de confirmations ...</note>
- <code>toIgnore</code> paramètres multiples définissant le type de chaine à ignorer lors de la génération via l'énumération <code>SaboCore\Utils\String\RandomStringType</code>

## Utilisation

<code-block lang="php">RandomStringGenerator::generateString(15,false,RandomStringType::UPPERCHARS)</code-block>

