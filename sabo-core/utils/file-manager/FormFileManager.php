<?php

namespace SaboCore\Utils\FileManager;

use Override;
use SaboCore\Routing\Response\DownloadResponse;
use SaboCore\Treatment\TreatmentException;
use SaboCore\Utils\Storage\AppStorage;

/**
 * @brief Gestionnaire de fichier provenant de formulaire ($_FILES)
 * @author yahaya bathily
 */
class FormFileManager extends FileManager{
    /**
     * @var array Données du fichier au format dans $_FILES
     */
    protected array $fileDatas;

    public function __construct(array $fileDatas){
        parent::__construct(fileAbsolutePath: $fileDatas["tmp_name"]);

        $this->fileDatas = $fileDatas;
    }

    /**
     * @param string|null $fileName
     * @inheritDoc
     * @attention fonction inactive pour les fichiers de formulaire, passer par FileManager
     */
    #[Override]
    public function getToDownload(?string $fileName = null): DownloadResponse{
        throw new TreatmentException(message: "Ce fichier ne peut pas être téléchargé",isDisplayable: true);
    }

    #[Override]
    public function storeIn(string $path, bool $createFoldersIfNotExists = true): bool{
        return
            $this->getErrorState() == 0 &&
            AppStorage::storeFormFile(
                storagePath: $path,
                fileTmpName: $this->fileAbsolutePath,
                createFoldersIfNotExists: $createFoldersIfNotExists
            );
    }

    /**
     * @inheritDoc
     * @attention fonction inactive pour les fichiers de formulaire, passer par FileManager
     */
    #[Override]
    public function getFromStorage(): ?FileContentManager{
        return null;
    }

    /**
     * @inheritDoc
     * @attention fonction inactive pour les fichiers de formulaire, passer par FileManager
     */
    #[Override]
    public function delete(): bool{
        return false;
    }

    /**
     * @return string le type mime contenu du fichier
     */
    public function getType():string{
        return $this->fileDatas["type"] ?? "";
    }

    /**
     * @brief Vérifie si le type mime contenu du fichier est présent dans la liste fournie
     * @param string ...$typesToCheck
     * @return bool
     */
    public function isInTypes(string ...$typesToCheck):bool{
        return in_array(needle: $this->getType(),haystack: $typesToCheck);
    }

    /**
     * @return int l'état de l'erreur contenue
     */
    public function getErrorState():int{
        return $this->fileDatas["error"];
    }

    /**
     * @return int la taille contenu
     */
    public function getSize():int{
        return $this->fileDatas["size"];
    }
}