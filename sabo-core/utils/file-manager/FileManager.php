<?php

namespace SaboCore\Utils\FileManager;

use Override;
use SaboCore\Routing\Response\DownloadResponse;
use SaboCore\Treatment\TreatmentException;
use SaboCore\Utils\Storage\AppStorage;
use SaboCore\Utils\Storage\Storable;

/**
 * @brief Gestionnaire de fichier serveur
 * @author yahaya bathily
 */
class FileManager implements Storable {
    /**
     * @var string chemin complet du fichier
     */
    protected string $fileAbsolutePath;

    /**
     * @param string $fileAbsolutePath chemin absolue du fichier
     */
    public function __construct(string $fileAbsolutePath){
        $this->fileAbsolutePath = $fileAbsolutePath;
    }

    /**
     * @return bool si le fichier existe
     */
    public function fileExists():bool{
        return @file_exists(filename: $this->fileAbsolutePath);
    }

    /**
     * @brief Recherche l'extension du fichier
     * @param bool $fromFirstOccur Si true récupère le chemin à partir du premier "." rencontré (ex : file.blade.php = blade.php) sinon le dernier (ex : file.blade.php = php)
     * @param string $extensionSeparator séparateur d'extension "." par défaut
     * @return string|null l'extension trouvée ou
     */
    public function getExtension(bool $fromFirstOccur = true,string $extensionSeparator = "."):string|null{
        $extension = $this->fileAbsolutePath;

        // récupération de l'extension dans que la chaine résultat contient des séparateurs de chemin
        do {
            $pos = $fromFirstOccur ? @strpos(haystack: $extension,needle: $extensionSeparator) : @strrpos(haystack: $extension,needle:  $extensionSeparator);

            if ($pos === false) return null;

            $extension = @substr(string: $extension, offset: $pos + 1);
        }while(@str_contains(haystack: $extension,needle: "/") || str_contains(haystack: $extension,needle: "\\") );

        return $extension;
    }

    /**
     * @param string|null $fileName nom à donner au fichier téléchargé ou null pour conserver le nom par défaut
     * @return DownloadResponse le fichier au téléchargement
     * @throws TreatmentException en cas de fichier non téléchargeable
     */
    public function getToDownload(?string $fileName = null):DownloadResponse{
        return new DownloadResponse(ressourceAbsolutePath: $this->fileAbsolutePath,chosenName: $fileName);
    }

    /**
     * @return string le chemin du fichier contenu
     */
    public function getPath():string{
        return $this->fileAbsolutePath;
    }

    /**
     * @brief Stock le fichier dans le dossier de stockage (en conservant le fichier actuel)
     * @param string $path Chemin à partir du dossier de stockage comme racine (/)
     * @param bool $createFoldersIfNotExists si true et que le nouveau chemin contient des dossiers inexistants, ils seront créés
     * @return bool si le stockage a réussi
     */
    #[Override]
    public function storeIn(string $path,bool $createFoldersIfNotExists = true):bool{
        return AppStorage::storeClassicFile(
            storagePath: $path,
            fileBasePath: $this->fileAbsolutePath,
            createFoldersIfNotExists: $createFoldersIfNotExists
        );
    }

    /**
     * @brief Supprime le fichier
     * @return bool si la suppression a réussie
     */
    public function delete():bool{
        return @unlink(filename: $this->fileAbsolutePath);
    }

    /**
     * @return FileContentManager|null gestionnaire de contenu de fichier si échec de lecture du contenu
     * @attention adapté aux fichiers à contenu textuel
     */
    #[Override]
    public function getFromStorage():?FileContentManager{
        $fileContent = @file_get_contents(filename: $this->fileAbsolutePath);

        return $fileContent === false ? null : new FileContentManager(fileContent: $fileContent);
    }
}