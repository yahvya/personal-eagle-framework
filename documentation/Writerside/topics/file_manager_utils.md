# Gestionnaire de fichier

> Les class de cet utilitaire permettent de gérer le contenu de fichiers stockés et provenant de formulaire.

- <code>FileContentManager</code>
- <code>FileManager</code>
- <code>FormFileManager</code>

## FileContentManager

> Cet utilitaire prend en constructeur un contenu textuel de fichier et apporte des fonctions de conversions par défaut.

- <code>getJsonContent</code> converti le contenu textuel en tableau php <code>JSON</code>

## FileManager

> Cet utilitaire apporte des fonctions permettant la gestion d'un fichier existant ou non à partir du chemin absolu fourni. Elle implémente l'interface <code>Storable</code>

- <code>fileExist</code> alias à la fonction <code>file_exits</code> de PHP, fourni si le fichier lié au chemin fourni existe
- <code>getExtension</code> fourni l'extension du fichier fourni 
    <note>L'extraction de l'extension sur la position du dernier '.' rencontré dans le chemin</note>
- <code>getToDownload</code> fourni une <code>DownloadResponse</code> permettant de rendre le fichier au téléchargement
- <code>getPath</code> fourni le chemin absolu lié
- <code>storeIn</code> méthode provenant de l'interface permet de stocker le fichier dans le dossier de stockage de l'application
- <code>delete</code> alias à la fonction <code>unlink</code> de PHP, permet de supprimer le fichier lié
- <code>getFromStorage</code> génère une instance de <code>FileContentManager</code> à partir du contenu du fichier

### FromFileManager

> Cet utilitaire extends <code>FileManager</code> et est destiné aux fichiers provenant de formulaire.

Elle prend en constructeur les données du fichier

<code-block lang="php">new FormFileManager($_FILES["f"]);</code-block>

L'utilitaire certaines fonctionnalités propres aux fichiers formulaire :

- <code>isInTypes</code> vérifie si le type du fichier se trouve dans la liste fournie
- <code>getErrorState</code> fourni l'état d'erreur du fichier
- <code>getSize</code> fourni la taille

<warning>Les fonctions suivantes sont désactivées : <code>getToDownload</code> <code>getFromStorage</code> <code>delete</code> </warning>