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
php sabo\csabo initial
```

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

> Un groupe peut prendre des conditions d'accès aux liens dans le même format que les fonctions classiques (::get,::post...), une condition défini sur un groupe est appliqué à tous les liens se trouvant

```
Route::group("/compte",[

],[fn():bool => true,LoggedCond::class,...)])
```

### Les models

Les models représentent les tables de la base de données , la commande 'make:model' permet via des questions de créer simplement la base de son model

#### QueryBuilder

> Chaque modèle étendant de SaboModel vient avec les méthodes crud basé sur une constructeur de requête interne, la class QueryBuilder implémente les fonctionnalités bas niveau permettant de construire les requêtes les plus complexes, et permet via 'addSql' d'écrire manuellement le code SQL combiné à l'utilasation de méthodes telles que 'getAttributeLinkedColName' qui permettant d'utiliser le nom de ses attributs de class php pour faire référence aux noms de colonnes

Le QueryBuilder est une class conteneur de traits divisés pouvant être retrouvé dans 'sabo > model > system > query-builder' permettant la modification et la personnalisation des fonctionnalités de celle ci que vous pouvez faire.

### Extensions du framework

Les extension représentent des paquets de code sous format zip pouvant être ajoutés à un projet *sabo* pour y ajouter des fonctionnalités

La commande suivante permet l'ajout simple d'extension au projet

```
php sabo\csabo extension:add
```

[Cliquez ici pour retrouver les extensions disponibles](https://github.com/yahvya/sabo-extensions)
