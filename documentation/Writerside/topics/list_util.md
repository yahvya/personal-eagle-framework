# Liste

> L'utilitaire <code>SaboList</code> est fait pour fourni une liste au format tableau offrant des fonctionnalités de recherche.

La class implémente les interfaces <code>Countable</code> <code>Iterator</code> <code>Arrayable</code> qui permettent 

- l'itération via boucle <code>foreach</code>
    <code-block lang="php">foreach(new SaboList(datas: ["sabo","framework"]) as $element) var_dump($element);</code-block>
- récupération du nombre d'éléments via la méthode <code>count</code>

Les méthodes propres à la class permettent l'accès aux données accompagné de fonctions de recherche

- <code>getFirst</code> fourni le premier élément de la liste
- <code>getLast</code> fourni le dernier élément de la liste
- <code>setFinder</code> permet modifier la fonction de recherche par défaut de la class (qui se base sur l'égalité). *Référez-vous aux commentaires de la fonction pour plus de détails*
- <code>getDefaultFinder</code> fourni la fonction de recherche par défaut de la class
- <code>find</code> fonction majeure de recherche, elle prend en paramètres les éléments à faire correspondre dans la liste basée sur la fonction de recherche courante. Elle retourne une nouvelle instance de <code>SaboList</code> contenant les éléments correspondants.



