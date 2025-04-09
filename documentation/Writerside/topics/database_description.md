# Base de données

> Le framework embarque un mini ORM permettant l'interaction avec la base de données. Le système par défaut intégré se base sur MYSQL, mais la structure est faîtes de manière à pouvoir implémenter un système personnalisé.

## Configuration de la base de données

La configuration de la base de données se base sur le fichier d'environnement <code>src/configs/env.php</code> au niveau de <code>// configuration de la base de données</code>.

- Mettre le booléen d'initialisation à <code>true</code>, permet de spécifier à l'étape de routing de lancer la gestion de la base de données
- Spécifier le provider, une class qui extends de <code>SaboCore\Database\Default\Provider\DatabaseProvider</code> (par défaut <code>MysqlProvider</code>) permet d'avoir une class pouvant se charger d'initialiser les outils requis.
- Spécifier les données de connexion

## Les models

> Un model dans le contexte de *sabo* est une class permettant de représenter sous forme de code PHP une table / une vue de la base de données. Cette description passe via une [liste d'attributs](#les-attributs-de-description).

Les models sont chargés par défaut dans le dossier <code>src/models</code> et extends de la class <code>CustomModel</code> servant d'intermédiaire avec le framework.

Cette class permet également de définir des méthodes personnalisées pour les models.

## Les attributs de description

<note>Ces attributs servent à décrire les différents éléments de la class, afin de les associer à la base de données et se situent dans le namespace <code>SaboCore\Database\Default\Attributes</code></note>

- <code>TableName</code> attribut associé à la class permettant de spécifier le nom de la table représentée
- <code>Binary|Bool|Char|Decimal|Enum|Int|Json|Text|TinyInt|VarBinary|Varchar</code> avec comme suffixe <code>Column</code>. Ces types permettent de mapper les différentes colonnes possibles sur un type <code>Mysql</code>. *Référez-vous aux paramètres et commentaires de ces fonctions*
  <code-block lang="php">
    use SaboCore\Database\Default\Attributes\VarcharColumn; 
    #[VarcharColumn(columnName: "user_name",maxLen: 255,)]
    protected string $username;
  </code-block>
- <code>TimestampColumn</code> ce type spécial du framework (bien qu'existant sur MYSQL) sert de passage pour toutes données de temps (datetime, time ...) via timestamp. Le type associé derrière est un <code>INT</code>. Un élément portant cet attribut doit utiliser le type customisé <code>Timestamp</code>.
    <code-block lang="php">
        use SaboCore\Database\Default\Attributes\TimestampColumn;
        use SaboCore\Database\Default\CustomDatatypes\Timestamp;
        use Override;
        #[TimestampColumn(columnName: "joined_at")]
        protected Timestamp $joinedAt;
        #[Override]
        protected function beforeCreate(mixed $datas = []): self{
            parent::beforeCreate();
            $this->joinedAt = new Timestamp();
            return $this;
        }
    </code-block>
    ou
    <code-block lang="php">
        use SaboCore\Database\Default\Attributes\TimestampColumn;
        use SaboCore\Database\Default\CustomDatatypes\Timestamp;
        #[TimestampColumn(columnName: "joined_at")]
        protected Timestamp $joinedAt = new Timestamp();
    </code-block>
- <code>JoinedColumn</code> ce type spécial permet de représenter une jointure entre deux tables. Un élément portant cet attribut doit être associé au type <code>SaboList</code> des utilitaires qui contiendra la liste des éléments associés.
    <code-block lang="php">
        use SaboCore\Database\Default\Attributes\JoinedColumn;
        use SaboCore\Database\Default\CustomDatatypes\JoinedList;
        #[JoinedColumn(
            classModel: BannedUserModel::class,
            joinConfig: ["id" => "userId"]
        )]
        protected JoinedList $bannedUsers;
    </code-block>
    
    - Le paramètre <code>classModel</code> représente la class de table associée
    - Le paramètre <code>joinConfig</code> ce tableau permet de définir les clés de jointures. Il est indicé par le nom des attributs de la class actuelle pointant sur le nom des attributs de la class liée. *Cette structure permet la liaison via de multiples attributs*
    - Le troisième paramètre <code>loadOnGeneration</code> permet de définir si les models liés doivent être chargés automatiquement à la génération du model. *Celà permet notamment d'éviter le chargement infini entre deux models.*
        <note>Dans le cas où le chargement se veut manuel, la création d'une nouvelle instance <code>new JoinedColumn()->loadContent()</code>.</note>

<note>Pour créer un nouvel attribut il vous faut créer une class qui extends de <code>SaboCore\Database\Default\Attributes\TableColumn</code> dont les méthodes et le constructeur peuvent être redéfinies au besoin. Se baser sur un type existant est recommandé.</note>
  
## Les conditions d'affectation

> Les conditions d'affectation permettent d'ajouter des barrières à passer lors de l'affectation d'une valeur sur un attribut lié à une colonne. Ces conditions peuvent être posées via chaque attribut de description. Elles doivent toutes être validées afin que l'affection opère. À l'échec d'une condition une exception est levée avec le message d'erreur associé.

Les conditions par défaut se situent dans le namespace <code>SaboCore\Database\Default\Conditions</code>.

- <code>RegexCond</code> permet d'associer une regex pour valider la donnée fournie.
- <code>LenCond</code> permet d'associer une limitation en taille sur une donnée de type <code>chaine</code>.
- <code>JsonValidityCond</code> permet de vérifier que la donnée est un tableau pour la conversion json.
- <code>FilterCond</code> permet d'appliquer une vérification <code>filter_var</code>
- <code>EnumValidity</code> permet de vérifier que la donnée est acceptée dans l'énumération lié
- <code>DatetimeCond</code> vérifie que la chaine fournie est une <code>Datetime</code> correcte.
- <code>CallableCond</code> permet d'appeler une condition encapsulée dans une méthode statique

<note>Pour créer une nouvelle condition personnalisée, veuillez créer une class implémentant l'interface <code>SaboCore\Database\Default\Conditions\Cond</code>.</note>

## Les utilitaires de formatage de données

> Le formatage de donnée permet d'ajouter une couche d'abstraction entre le format fourni et récupéré. Les utilitaires de formatage bien qu'ayant la même structure se distinguent en deux groupes : ***reformer*** groupe qui permet à partir de la donnée stockée de formater à la récupération, ***formater*** formater une donnée avant de la stocker dans l'attribut. Ces conditions peuvent être posées via chaque attribut de description.

Les 'formater' par défaut se trouvent dans le namespace <code>SaboCore\Database\Default\Formatters</code>.

- <code>JsonFormatter</code> formate un tableau en chaine json pour le stockage en base de données.
- <code>JsonReformer</code> reformate une chaine json formatée en tableau.  

<note>Pour créer un nouveau formater, veuillez créer une class implémentant l'interface <code>SaboCore\Database\Default\Formatters\Formater</code></note>

## Les types personnalisés

> Les types customisés mentionnés plus haut, sont associé à des types d'attributs particuliers. L'attribut est donc l'élément qui défini le type à utiliser.

- <code>JoinedList</code> lié à <code>JoinedColumn</code>
- <code>Timestamp</code> lié à <code>TimestampColumn</code>

<note>Pour créer un type customisé, il suffit de créer une class, pour pouvoir l'utiliser, il faut également créer un nouvel attribut de description de colonne qui attendra une donnée du type de la class créée.</note>

## Les hooks

> Les hooks sont des méthodes appellés à des moments précis du cycle de vie d'un model (des évènements). Redéfinir ces méthodes permet d'intercepter et d'effectuer les actions à l'appel de ces évènements. 
<warning>Pensez à appeler les méthodes parentes, lors des redéfinitions <code>parent::beforeCreate();</code></warning>

L'énumération <code>SaboCore\Database\System\DatabaseActions</code> liste les hooks implémentées par les systèmes.

- <code>[before|after]Create</code> actions pre et post création
- <code>[before|after]Update</code> actions pre et post mise à jour
- <code>[before|after]Delete</code> actions pre et post suppression
- <code>[before|after]Generation</code> actions pre et post génération du model. *L'étape de génération consiste à l'affectation des valeurs de la base de données et autres actions de création du model*.

## Les méthodes par défaut

> En plus des hooks certaines méthodes utilitaires sont implémentées par défaut.

**Cette liste ne contient pas toutes les méthodes définies, seulement les plus utiles.**

- <code>create</code> enregistre le model
- <code>update</code> met à jour le model (basé sur les éléments déclarés comme clés primaires)
- <code>delete</code> supprime le model (basé sur les éléments déclarés comme clés primaires)
- <code>findOne</code> méthode statique permettant de récupérer un seul élément matchant les conditions
- <code>findAll</code> méthode statique permettant de récupérer les éléments matchant les conditions
<note>Les fonctions <code>findOne</code> et <code>findAll</code> se basent sur la même logique de construction de requête que le <code>QueryBuilder</code></note>
- <code>setAttribute</code> met à jour la valeur d'un attribut en vérifiant en amont les conditions associées puis en appliquant les formateurs associés
- <code>getAttribute</code> récupère la donnée d'un attribut en appliquant les reformers associés
- <code>getAttributeOriginal</code> permet de récupérer la valeur originale stockée sans reformer
- <code>getColumnsConfig</code> fourni la configuration récupérée des colonnes.
- <code>getColumnConfig</code> fourni la configuration d'une colonne particulière via le nom de l'attribut de class associé
- <code>getJoinedColumnsConfig</code> fourni la configuration des colonnes de jointure
- <code>getTableNameManager</code> fourni une instance de l'attribut <code>TableName</code> du nom de la table
- <code>getAsArray</code> enregistre les données de la class dans un tableau indicé par le nom d'attribut de class
- <code>setAttributesOriginalValues</code> met à jour le tableau des valeurs originales 
  <warning>Ne mets pas à jour les valeurs dans la class</warning>
- <code>lastInsertId</code> fourni le dernier id inséré (peut être utile notamment pour l'affectation des clés primaires après insertion)
- <code>createFromDatabaseLine</code> génère un model à partir d'une ligne de la base de données *(via fetch)*
- <code>createFromDatabaseLines</code> génère les models à partir des lignes de la base de données *(via fetch)*
- <code>createFromDatabaseLines</code> génère les models à partir des lignes de la base de données *(via fetch)*
- <code>newInstanceOfModel</code> génère une nouvelle instance du model à partir de la class fournie
- <code>execQuery</code> exécute une requête via le constructeur de requête
- <code>loadJoinedColumns</code> charge les colonnes jointes
- <code>createModelFromLine</code> génère un nouveau model à partir d'une ligne directe (sous forme de tableau) de la base de données
- <code>buildPrimaryKeysCondOn</code> ajoute les conditions de clés primaires sur un constructeur de requête
- <code>getDatabaseConfig</code> fournie la configuration sous forme de class <code>Config</code> de base de données définie dans l'environnement

## Générer le SQL de création d'un model

> L'utilitaire de génération permet à partir d'une instance d'un modèle, de générer le SQL de création de la table

<code-block lang="php">
&lt;?php
use SaboCore\Database\Default\System\MysqlTableCreator;
echo MysqlTableCreator::getTableCreationFrom(model: new YourModelInstance);
</code-block>