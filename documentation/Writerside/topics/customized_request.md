# Construction de requêtes customisées

> Les requêtes customisées passent par l'utilisation de plusieurs utilitaires différents permettant de construire via des méthodes PHP une requête SQL différente des méthodes classiques CRUD.

## MysqlQueryBuilder

L'utilitaire principale, le constructeur de requête <code>SaboCore\Database\Default\QueryBuilder\MysqlQueryBuilder</code>

<code-block lang="php">
&lt;?php
use SaboCore\Database\Default\QueryBuilder\MysqlQueryBuilder;
MysqlQueryBuilder::createFrom(modelClass: YourModel::class);
</code-block>

- <code>reset</code> remet à 0 les éléments internes du constructeur
- <code>prepareRequest</code> prépare la requête dans l'instance de PDO fournie
- <code>as</code> défini l'alias dans la requête
    <warning>Un alias par défaut est associé par défaut à chaque constructeur</warning>
- <code>getRealSql</code> fourni le SQL interne sans modification
- <code>getSql</code> fourni le SQL généré
- <code>getBaseModel</code> fourni le model lié au constructeur
- <code>getBindValues</code> fourni une liste de class gestionnaires de *bind*
- <code>joinBuilder</code> permet de joindre le constructeur fourni au constructeur actuel (utilisé pour l'imbrication de requête)
- <code>staticRequest</code> permet d'écrire manuellement une requête SQL
    <warning>Si possible mieux vaux utiliser les fonctions de constructions</warning>

<note>Les fonctions suivantes permettent de construire la requête SQL partie par partie. Les commentaires associés décrivent plus en détails chacun des paramètres.</note>

### Select

> La méthode <code>select</code> permet de définir les éléments à sélectionner dans la requête. Elle attend des noms d'attribut ou des fonctions SQL encapsulées dans <code>MysqlFunction</code>. Si aucun paramètre n'est fourni alors <code>SELECT *</code>

*Cet exemple montre uniquement les possibilités*

<code-block lang="php">
$builder->select("username",MysqlFunction::COUNT(numberGetter: "`{price}`"));
</code-block>

### Insert

> La méthode <code>insert</code> permet de définir une requête d'insertion. Elle attend un tableau indicé par les noms d'attributs de class associé aux colonnes avec comme valeur, les données à insérer.

Les données à insérer peuvent être sous la forme : <code>brute</code>, <code>MysqlFunction</code> ou <code>MysqlQueryBuilder</code>

*Cet exemple montre uniquement les possibilités*

<code-block lang="php">
$builder->insert(insertConfig: [
    "username" => "yahvya",
    "github" => "https://github.com/yahvya",
    "fullname" => MysqlFunction::UPPER("{username}"),
    "queryResult" => MysqlQueryBuilder::createFrom(modelClass: YourModel::class)
        ->select()
        ->limit(count: 1)
]);
</code-block>

### Update

> La méthode <code>update</code> permet de définir une requête de mise à jour. Elle attend un tableau indicé par les noms d'attributs de class associé aux colonnes avec comme valeur, les données à mettre à jour.

Les données à mettre à jour peuvent être sous la forme : <code>brute</code>, <code>MysqlFunction</code> ou <code>MysqlQueryBuilder</code>

*Cet exemple montre uniquement les possibilités*

<code-block lang="php">
$builder->update(insertConfig: [
    "username" => "yahvya",
    "github" => "https://github.com/yahvya",
    "fullname" => MysqlFunction::UPPER("{username}"),
    "queryResult" => MysqlQueryBuilder::createFrom(modelClass: YourModel::class)
        ->select()
        ->limit(count: 1)
]);
</code-block>

### Delete

> La méthode <code>delete</code> permet de définir une requête de suppression. 

<code-block lang="php">
$builder->delete();
</code-block>

### Where

> La fonction <code>where</code> permet d'ajouter le mot clé <code>WHERE</code> sur la requête

<code-block lang="php">
$builder->select()->where(); # équivaux à SELECT * from ... WHERE
</code-block>

### Cond

> La fonction <code>cond</code> permet de paire avec <code>where</code>, de définir les conditions where via un ensemble de méthodes. Elle attend <code>MysqlCondition</code> la classe de gestion des conditions ou <code>MysqlCondSeparator</code> permettant de séparer les conditions, créer des groupes ...

*Cet exemple montre uniquement les possibilités*

<code-block lang="php">
$builder
    ->select()
    ->where()
    ->cond(
        new MysqlCondition(condGetter: "username",comparator: MysqlComparator::EQUAL(),conditionValue: "yahvya"),
        MysqlCondSeparator::AND(),
        new MysqlCondition(condGetter: "email",comparator: MysqlComparator::EQUAL(),conditionValue: "sabo.framework@github.com"),
    );
</code-block>

### Having

> La fonction <code>having</code> permet d'ajouter la clause <code>HAVING</code> à la requête, sa construction se base sur la [même logique de construction que la fonction <code>cond</code>](#cond)

### Order By

> La fonction <code>orderBy</code> permet d'ajouter la clause <code>orderBy</code> à la requête. Elle prend en paramètre un tableau contenant une liste au format [nom d'attribut, "ASC|DESC]

<code-block lang="php">
$builder
    ->select()
    ->orderBy(["price","ASC"],["id","DESC"]);
</code-block>

### Group By

> La fonction <code>groupBy</code> permet d'ajouter la clause <code>GROUP BY</code> à la requête. Elle prend en paramètre la liste des noms d'attributs de classe.

*Cet exemple montre uniquement les possibilités*

<code-block lang="php">
$builder
    ->select(MysqlFunction::COUNT(numberGetter: "{price}"))
    ->groupBy("price","id");
</code-block>

### Limit

> La fonction <code>limit</code> permet d'ajouter la clause <code>LIMIT</code> à la requête. Elle prend en paramètre le nombre d'éléments et optionnellement l'offset.

<code-block lang="php">
$builder
    ->select()
    ->limit(count: 1);
$builder
    ->select()
    ->limit(count: 15,offset: 30);
</code-block>

## MysqlComparator

> Cette class sert à ajouter un symbole de comparaison. Elle contient par défaut un panel de fonctions représentant différents modes de comparaison. Fiez-vous aux commentaires de ces fonctions pour l'utilisation.

<note>Certains comparateurs attendent un type de données implicites, <code>ex: IN()</code> attend que la donnée fournie derrière soit un tableau contenant les valeurs à vérifier. Les commentaires définissent les requis</note>
<warning>Veuillez utiliser le plus possible les fonctions déjà définies et éviter d'utiliser le constructeur.</warning>

**Focus sur la méthode spéciale REQUEST_COMPARATOR**

> Cette méthode permet de comparer une valeur avec le résultat d'une requête <code>ex: WHERE ID = (SELECT id from table)</code>.

Elle prend en paramètre une chaine comparator spéciale, car permettant de choisir l'emplacement de la requête.

Pour une requête du style <code>SELECT * FROM table_1 where id = IN(SELECT id from table_2 LIMIT 4)</code>

<code-block lang="php">
$tableOneBuilder = MysqlQueryBuilder::createFrom(modelClass: TableOneModel::class);
$tableTwoBuilder = MysqlQueryBuilder::createFrom(modelClass: TableTwoModel::class);
$tableTwoBuilder
    ->select("id")
    ->limit(count: 4);
$tableOneBuilder
    ->select();
    ->where()
    ->cond(new MysqlCondition(
        condGetter: "id",
        comparator: MysqlComparator::REQUEST_COMPARATOR(comparator: "IN({request})",queryBuilder: $tableTwoBuilder),
        conditionValue: $tableTwoBuilder
    ));
</code-block>

## MysqlCondition

> Cette class permet de définir une condition sql basé sur trois paramètres, <code>condGetter</code> le nom d'un attribut ou <code>MysqlFunction</code>, <code>comparator</code> le comparateur, <code>conditionValue</code> la valeur requise par le comparateur utilisé

## MysqlCondSeparator

> Cette class permet d'ajouter des bouts de chaine SQL, notamment des mots clés de séparation. Elle peut être donc être utilisé pour créer des groupes de condition en ajoutant ( et ) via GROUP_START et GROUP_END ou des mots clés customisés

<code-block lang="php">
$builder
    ->select()
    ->where()
    ->cond(
        MysqlCondSeparator::GROUP_START(),
            new MysqlCondition(...),
            new MysqlCondSeparator::AND(),
            new MysqlCondition(...),
        MysqlCondSeparator::GROUP_END()
        new MysqlCondSeparator::OR(),
        new MysqlCondition(...)
    );
</code-block>