<?php

namespace SaboCore\Routing\Response;

use Override;
use Throwable;

/**
 * @brief Réponse de téléchargement
 * @author yahaya bathily https://github.com/yahvya
 */
class DownloadResponse extends Response{
    /**
     * @param string $ressourceAbsolutePath chemin absolu du fichier à fournir
     * @param string|null $chosenName nom à donner au fichier à télécharger, si null nom par défaut utilisé
     * @attention le fichier fourni doit exister
     */
    public function __construct(string $ressourceAbsolutePath,?string $chosenName = null){
        $this->content = $ressourceAbsolutePath;

        if(@file_exists(filename: $ressourceAbsolutePath) ){
            $this
                ->setHeader(name: "Content-Description",value: "File Transfer")
                ->setHeader(name: "Content-Type",value: "application/octet-stream")
                ->setHeader(name: "Content-Disposition",value:  "attachment; filename=" . ($chosenName ?? basename(path: $ressourceAbsolutePath) ) )
                ->setHeader(name: "Expires",value: "0")
                ->setHeader(name: "Cache-Control",value: "must-revalidate")
                ->setHeader(name: "Pragma",value: "public");

            // récupération de la taille du fichier
            $fileSize = @filesize($ressourceAbsolutePath);

            if($fileSize !== false)
                $this->setHeader(name: "Content-Length",value: $fileSize);
        }
    }

    #[Override]
    protected function renderContent():never{
        try{
            @readfile(filename: $this->content);
        }
        catch(Throwable){
            die("Ressource non trouvé");
        }

        die();
    }
}