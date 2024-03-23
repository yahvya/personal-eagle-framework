<?php

namespace SaboCore\Utils\FileManager;

use SaboCore\Treatment\TreatmentException;

/**
 * @brief Gestionnaire de contenu de fichiers
 * @author yahaya bathily https://github.com/yahvya
 */
class FileContentManager{
    /**
     * @var string contenu du fichier
     */
    protected string $fileContent;

    /**
     * @param string $fileContent le contenu associé
     */
    public function __construct(string $fileContent){
        $this->fileContent = $fileContent;
    }

    public function getContent():string{
        return $this->fileContent;
    }

    /**
     * @return array le contenu du fichier au format json
     * @throws TreatmentException si le fichier n'est pas convertible, displayable non affichable
     */
    public function getJsonContent():array{
        $convertedContent = @json_decode(json: $this->fileContent,associative: true);

        if(gettype(value: $convertedContent) !== "array")
            throw new TreatmentException(message: "Le fichier ne peut être converti au format json",isDisplayable: false);

        return $convertedContent;
    }
}