# Mini framework - Sabo

> Sabo est un mini framework php visant à faciliter le développement d'applications web tout en conservant le plaisir d'écrire du PHP.

*Le site de documentation du framework est en cours de création*

### Créer un projet

Veuillez suivre les étapes suivantes:

```
mkdir <monprojet>
cd <monprojet>
git clone https://github.com/yahvya/sabo-final.git .
php sabo\csabo initialize
```

***Utiliser un système d'hôte virtuel est à préconiser - toutefois une configuration pour des liens au format localhost/projet est possible***

Vous pouvez maintenant vous rendre sur votre navigateur ex *https://monprojet.local*

*Une version usant d'un serveur personnalisé sera bientôt disponible*

#### Pour les utilisateurs de laragon

Vous pouvez configurer laragon pour ajouter dans l'option 'Créer un site web rapidement' le framework pour cela:

- Téléchargez directement le zip via github > code > download zip
- Conservez le fichier au format zip dans `<emplacement>`
- Allez ensuite dans les paramètres de laragon (clic droit sur l'icône)
- Placez votre curseur sur **'créer un site web rapidement'**
- Appuyez sur '**configuration'**
- Dans le fichier ouvert ajoutez la ligne suivante en remplaçant *emplacement* par le chemin absolue de stockage du zip *ex: C:*

  ```
  Sabo=emplacement
  ```

A la création d'un nouveau site via l'option, veuillez saisir dans le terminal à la racine du projet

```
php sabo\csabo initialize
```

>  Le framework initialisera votre site avec une connexion à la base de donnée, configurable dans 'config/env.json' , si vous ne souhaitez pas une connexion à la base de donnée rendez vous dans 'config/sabo/config.php' et changez à **false** INIT_WITH_DATABASE_CONNEXION

### Structure du projet

- app : *dossier contenant les élements composer de l'utilisateur - model psr-4 de chargement ajouté par défaut*
- config: *dossier de configuration du framework*
  - env.json : *fichier d'environnement du projet (remplacable par .env en fonction de la configuration)*
  - sabo : *dossier de configuration d'éléments techniques*
    - config.php : *fichier permettant de configurer le framework (type de fichier env,maintenance,pages par défaut....)*
    - functions.php : *fonctions globales au programme*
  - routes : *dossier des routes du projet*
    - routes.php : *fichier principal des routes*
    - routes : *dossier contenant les sous routes utilisés par (Route::getFromFile("personnalise") )*
      - personnalise.php*
- public : *dossier contenant les élements autorisés publiquement d'accès avec par défaut*
  - css
  - icons
  - js
- sabo : *code du framework (modifiable mais non recommandé) - élements les plus importants:*
  - cli : *dossier des commandes php sabo\csabo - vous pouvez y ajouter vos commandes en étendant de la classe SaboCliCommand*
  - config : *contient des énumérations de configuration sur la structure du framework - vous pouvez y modifier des éléments*
  - model: *dossier contenant les codes racines des futurs model avec mysql (par défaut) et postgree comme système implémentés*
    - attribute : *dossier contenant les *attributs de description de model**
    - cond : *dossier contenant les conditions par défaut implémenté pouvant être posés sur les colonnes*
  - utils : *dossier contenant les utilitaires d'api et de chaine*
  - vendor + composer.json + composer.lock : **A NE PAS TOUCHER**
  - csabo : *fichier d'enregistrement des commandes 'php sabo\csabo' - vous pouvez y enregistrer vos propes commandes*
  - index.php : *point d'entrée des liens*
- src : *dossier principal utilisateur - contient le code source ajouté*
  - controller : *dossier par défaut des controllers*
  - mail : *dossier contenant les template twig de mail*
  - middleware:
    - middleware : *dossier prévu pour contenir les middlewares*
    - routes-cond : dossier prévu pour contenir les conditions d'accès de lien (authentification...)
  - model : *dossier par défaut des model*
  - view : *dossier racine de la recherche de vues twig*
- .htaccess - modifiable en conservant la redirection du framework

### Commandes

Le framework vient avec un petit utilitaire de commande nommé 'csabo' pour 'cli sabo'. Le fichier est un fichier php sans extension situé dans le dossier sabo.

Pour l'utiliser à partir de la racine

```
php sabo\csabo --showlist
```

> Vous pouvez y ajouter vos commandes en étendant la class SaboCliCommand et en enregistrant votre commande en suivant le modèle dans csabo

*Les commandes de création de fichier telles que make:controller ou make:model se basent sur un fichier modèle se situant dans le dossier sabo > cli > resources > model, modifier ces fichiers changera le template généré à l'utilisation de ces commandes.*

### Afficher une page / Routing

#### Le fichier routes.php

Dans le fichier config > routes > routes.php vous pouvez ajouter vos routes à l'aide de la class 'Sabo\Sabo\Route'

La class offre différentes méthodes statiques ::get,::post,::put,::delete,::group pour générer vos routes

```
return Route::generateFrom([
	Route::get("/",function():void{
		echo "Sabo framework";
	},"home.homepage")
]);
```

Ces fonctions (hormis ::group) acceptent un callable de type fonction comme dans l'exemple ou une sous classe de 'Sabo\Controller\Controller\SaboController'

```
return Route::generateFrom([
	Route::get("/",[HomeController::class,"showHomepage"],"home.homepage")
]);
```

#### Paramètres génériques

Pour les paramètres de liens génériques ex:

- /article/article-1
- /article/article-2

l'utilisation d'un paramètre générique dans le lien est nécessaire

```
Route::get("/article/{articleName}",.....)
```

ce paramètre représentera le nom saisi dans l'url et peut être récupéré par injection dans la fonction de gestion de même pour le controller

```
Route::get("/article/{articleName}",function(string $articleName):void{
	echo "nom de l'article : {$articleName}";
},"article.article");
```

Le format de ces paramètres peut être géré via expression régulière non englobantes (n'usant pas de **()** )

```
Route::get("/article/{articleName}",function(string $articleName):void{},"home.homepage",["articleName" => "[a-zA-Z0-9\-\']+"])
```

#### Condition d'accès aux liens

en plus de la forme du lien des conditions peuvent être ajoutés pour autoriser l'accès au lien (par exemple : l'utilisateur doit être connecté), ce conditions se matérialisent par des fonctions renvoyant des booléens ou des class de conditions décrivant des conditions plus complètes

```
Route::get("/login",function():void{echo "page de connexion"},accessConds: [
        fn():bool => true,
        function():void{
            if(...) return true;

            // actions ou 
            return false;
        },
        ClassCond::class
    ])
```

Toutes les conditions se doivent de retourner true pour que l'accès soit valide

'ClassCond::class' représente une class impémentant l'interface 'Sabo\Middleware\Middleware\SaboMiddlewareCond', cette interface défini une fonction verify qui sera appellé pour vérifier vos conditions , si la fonction retourne false la fonction 'toDoOnFail' sera appellé (peut servir à rediriger ...)

#### Groupes de liens

La méthode 'Route::group' permet de grouper des liens avec un préfixe commun et peuvent être imbriqués ex:

```
Route::group("/compte",[
	Route::get("/"),
	Route::get("/deconnexion"),
	Route::group("/gestion",[
		Route::get("/")
	])
])
```

Cet exemple génère 3 liens:

- /compte/
- /compte/deconnexion
- /compte/gestion/

> Un groupe peut prendre des conditions d'accès aux liens dans le même format que les fonctions classiques (::get,::post...), une condition défini sur un groupe est appliqué à tous les liens se trouvant à l'intérieur

```
Route::group("/compte",[

],[fn():bool => true,LoggedCond::class,...)])
```

### Les models

Les models représentent les tables de la base de données , la commande 'make:model' permet via des questions de créer simplement la base de son model

#### QueryBuilder

> Chaque modèle étendant de SaboModel vient avec les méthodes crud basé sur un constructeur de requête interne, la class QueryBuilder implémente les fonctionnalités bas niveau permettant de construire les requêtes les plus complexes, et permet via 'addSql' d'écrire manuellement le code SQL combiné à l'utilasation de méthodes telles que 'getAttributeLinkedColName' qui permettant d'utiliser le nom de ses attributs de class php pour faire référence aux noms de colonnes

Le QueryBuilder est une class conteneur de traits divisés pouvant être retrouvé dans 'sabo > model > system > query-builder' permettant la modification et la personnalisation des fonctionnalités de celle ci que vous pouvez faire.

exemple de model contenant des requêtes simple et plus complexe

```
<?php

namespace Model\Model;

use PDO;
use Sabo\Model\Attribute\TableColumn;
use Sabo\Model\Attribute\TableName;
use Sabo\Model\Cond\DatetimeCond;
use Sabo\Model\Cond\PrimaryKeyCond;
use Sabo\Model\Cond\RegexCond;
use Sabo\Model\Cond\VarcharCond;
use Sabo\Model\Model\SaboModel;
use Sabo\Model\System\Mysql\MysqlReturn;
use Sabo\Model\System\QueryBuilder\QueryBuilder;
use Sabo\Model\System\QueryBuilder\SqlComparator;
use Sabo\Model\System\QueryBuilder\SqlSeparator;

/**
 * table des articles de blog
 * @name BlogModel
 */
#[TableName("blog")]
class BlogModel extends SaboModel{
	#[TableColumn("id",false,new PrimaryKeyCond(true) )]
	protected int $id;

	#[TableColumn("article_title",false,new VarcharCond(2,255,"Le titre doit contenir entre 2 et 255 caractères") )]
	protected string $title;

	#[TableColumn("formatted_title",false,new RegexCond("[a-zA-Z-'0-9]{2,255}","Veuillez vérifier le format du titre formaté. Il doit contenir entre 2 et 255 caractères."))]
	protected string $formattedTitle;

	#[TableColumn("article_preview",false,new VarcharCond(2,255,"Le contenu preview de l'article doit contenir entre 2 et 255 caractères") )]
	protected string $preview;

	#[TableColumn("article_content",false,new RegexCond(".{10,}","L'article doit être assez conséquent pour être affiché") )]
	protected string $content;

	#[TableColumn("create_date",false,new DatetimeCond() )]
	protected string $creationDate;

	#[TableColumn("is_active",false)]
	protected bool $isActive;

    public function insert(): bool{
        $this->isActive = true;
        $this->creationDate = date("Y-m-d H:i:s");

        return parent::insert();
    }

    /**
     * trouve un article à partir d'une recherche
     * @param string $search recherche
     * @return BlogModel|null l'article trouvé ou null
     */
    public static function getFromSearch(string $search):?BlogModel{
        $queryBuilder = QueryBuilder::createFrom(self::class);

        $queryBuilder
            ->select()
            ->where()
            ->whereCond("title","%{$search}%",SqlComparator::LIKE,SqlSeparator::AND)
            ->whereCond("isActive",true)
            ->orderBy("title")
            ->limit(1);

        $results = self::execQuery($queryBuilder,MysqlReturn::OBJECTS);

        return empty($results) ? null : $results[0];
    }

    /**
     * recherche un article aléatoire
     * @return BlogModel|null l'article trouvé ou null
     */
    public static function getRandomArticle():?BlogModel{
        $queryBuilder = QueryBuilder::createFrom(self::class);

        $queryBuilder
            ->select()
            ->where()
            ->whereCond("isActive",true)
            ->addSql("ORDER BY RAND() ")
            ->limit(1);

        $results = self::execQuery($queryBuilder,MysqlReturn::OBJECTS);

        return !empty($results) ? $results[0] : null;
    }

    /**
     * recherche les articles similaires à celui donné
     * @param BlogModel $baseArticle article de base
     * @param int $countOfSimilarArticlesToGet nombre d'articles maximum similaire à récupéré
     * @return array liste des articles trouvés
     */
    public static function getSimilarArticlesToOrRandoms(BlogModel $baseArticle, int $countOfSimilarArticlesToGet):array{
        /*
            modèle de requete (similarité vérifié dans les 2 sens, article 1 ressemble au 2 ou article 2 ressemble au 1)

            # sauvegarde des informations de l'article
            WITH blog_article_data AS (SELECT ba.article_title,ba.article_content FROM blog AS ba WHERE id = 2 AND is_active 1)
            SELECT
                *
            FROM
                blog AS b
            WHERE
            #     exclusion de l'article lui même dans les résultats
                b.id != 2 AND
                b.is_active = 1 AND
                (
            #         sélection d'un article dont les caractéristiques ressemblent à celui de l'article de base
                    (SELECT article_title FROM blog_article_data) LIKE CONCAT('%',b.article_title,'%') OR
                    (SELECT article_content FROM blog_article_data) LIKE CONCAT('%',b.article_content,'%') OR
                    (SELECT article_content FROM blog_article_data) LIKE CONCAT('%',b.article_title,'%') OR
            #         sélection d'un article auquel les caractéritiques de l'article de base ressemble
                    b.article_title LIKE CONCAT('%',(SELECT article_title FROM blog_article_data),'%') OR
                    b.article_content LIKE CONCAT('%',(SELECT article_content FROM blog_article_data),'%') OR
                    b.article_title LIKE CONCAT('%',(SELECT article_content FROM blog_article_data),'%')
                )
        */

        $queryBuilder = QueryBuilder::createFrom(self::class);

        // nom des élements sql génériques (nom de table,colonnes...)
        $tableName = $queryBuilder->getLinkedModel()->getTableName();
        $title = $queryBuilder->getAttributeLinkedColName("title");
        $content = $queryBuilder->getAttributeLinkedColName("content");
        $id = $queryBuilder->getAttributeLinkedColName("id");
        $isActive = $queryBuilder->getAttributeLinkedColName("isActive");

        $queryBuilder
            ->addSql(
                "
                    WITH blog_article_data AS (SELECT ba.{$title},ba.{$content} FROM {$tableName} AS ba WHERE {$id} = ? AND {$isActive} = 1)
                    SELECT
                        *
                    FROM
                        {$tableName} AS b
                    WHERE
                        b.{$id} != ? AND
                        b.{$isActive} = 1 AND
                        (
                            (SELECT {$title} FROM blog_article_data) LIKE CONCAT('%',b.{$title},'%') OR
                            (SELECT {$content} FROM blog_article_data) LIKE CONCAT('%',b.{$content},'%') OR
                            (SELECT {$content} FROM blog_article_data) LIKE CONCAT('%',b.{$title},'%') OR
                            b.{$title} LIKE CONCAT('%',(SELECT {$title} FROM blog_article_data),'%') OR
                            b.{$content} LIKE CONCAT('%',(SELECT {$content} FROM blog_article_data),'%') OR
                            b.{$title} LIKE CONCAT('%',(SELECT {$content} FROM blog_article_data),'%')
                        ) 
                ",
                [$baseArticle->id,$baseArticle->id ]
            )
            ->limit($countOfSimilarArticlesToGet);

        $results = self::execQuery($queryBuilder, MysqlReturn::OBJECTS);

        // si aucun résultat trouvé recherche d'articles randoms
        if(empty($results) ){
            $queryBuilder
                ->reset()
                ->select()
                ->where()
                ->whereCond("id",$baseArticle->id,SqlComparator::NOT_EQUAL)
                ->limit($countOfSimilarArticlesToGet);

            $results = self::execQuery($queryBuilder,MysqlReturn::OBJECTS);
        }

        return $results;
    }
}
```

Cet exemple sert à montrer :

- La modification possible du namespace de base fourni
- requêtes simples
- requêtes plus complexes
- l'ajout des conditions sur un attribut via les conds
- la recommandation de définir une couche entre SaboModel et vos classes (ici : abstract class CustomModel extends SaboModel)

#### Conds

Pour mettre à jour une valeur attribut d'un model ou récupérer une valeur l'usage des fonctions '*setAttribute' et 'getAttribute'* est nécessaire. Ces fonctions prennent en premier argument le nom de l'attribut / variable comme référence au nom de la colonne en base de donnée

A l'usage de setAttribute sur un champs *'setAttribute("title","Nouveau titre")'* chaque conditions posés sur 'protected string $title;' sera vérifié et la première invalide renvoie une exception (ModelCondException)

### Middleware

La class abstraite *SaboMiddleware* offre les fonctionnalités de base pour la gestion des exceptions de model , la vérification de champs ....

### Extensions du framework

Les extension représentent des paquets de code sous format zip pouvant être ajoutés à un projet *sabo* pour y ajouter des fonctionnalités

La commande suivante permet l'ajout simple d'extension au projet

```
php sabo\csabo extension:add
```

[Cliquez ici pour retrouver les extensions disponibles](https://github.com/yahvya/sabo-extensions)
