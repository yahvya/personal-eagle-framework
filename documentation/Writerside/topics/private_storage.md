# Stockage protégé

> En plus du dossier <code>public</code> permettant de stocker des ressources accessibles publiquement, le framework vient avec un dossier <code>src/storage</code> offrant un espace de stockage accessible seulement via l'application.

L'accès à ce stockage peut se faire via l'utilitaire <code>SaboCore\Utils\Storage\AppStorage</code>. *Référez-vous aux commentaires des fonctions pour plus de détails*.

Cette class offre des fonctions de stockage et d'accès :

- <code>storeClassicFile</code> stocke une copie d'un fichier dans le dossier de stockage. Le paramètre <code>storagePath</code> prend comme racine le dossier <code>storage</code>
    <code-block lang="php">AppStorage::storeClassicFile(storagePath: "/articles/new.md",fileBasePath: "chemin_absolue_vers_fichier.txt");</code-block>
- <code>storeContent</code> stocke un contenu textuel dans le dossier de stockage. Le paramètre <code>storagePath</code> prend comme racine le dossier <code>storage</code>
    <code-block lang="php">AppStorage::storeContent(storagePath: "/articles/new.md",content: "# Sabo framework doc");</code-block>
- <code>storeFormFile</code> stocke un fichier de formulaire à partir du tmp name. Le paramètre <code>storagePath</code> prend comme racine le dossier <code>storage</code>
    <code-block lang="php">AppStorage::storeFormFile(storagePath: "/articles/new.md",fileTmpName: $_FILES["f"]["tmp_name"]);</code-block>
- <code>buildStorageCompletePath</code> construis le chemin absolu vers le dossier stockage en fournissant un chemin l'ayant comme racine.
    En partant d'un dossier souhaité <code>src/storage/articles</code> pour avoir le chemin absolu via cette fonction 
    <code-block lang="php">AppStorage::buildStorageCompletePath(pathFromStorage: "/articles");</code-block>

## Storable

> L'interface <code>Storable</code> du même namespace permet la description d'un élément pouvant être stocké