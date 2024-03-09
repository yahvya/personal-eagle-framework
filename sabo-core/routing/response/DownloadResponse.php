<?php

namespace SaboCore\Routing\Response;

use Override;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

/**
 * @brief Réponse de téléchargement
 * @author yahaya bathily https://github.com/yahvya
 */
class DownloadResponse extends Response{
    /**
     * @param string $ressourceAbsolutePath chemin absolu du fichier à fournir
     * @attention le fichier fourni doit exister
     */
    public function __construct(string $ressourceAbsolutePath){
        $this->content = $ressourceAbsolutePath;

        if(file_exists($ressourceAbsolutePath) ){
            $this
                ->setHeader("Content-Description","File Transfer")
                ->setHeader("Content-Type","application/octet-stream")
                ->setHeader("Content-Disposition", "attachment; filename=" . basename($ressourceAbsolutePath) )
                ->setHeader("Expires","0")
                ->setHeader("Cache-Control","must-revalidate")
                ->setHeader("Pragma","public")
                ->setHeader("Content-Length",filesize($ressourceAbsolutePath) );
        }
    }

    #[Override]
    protected function renderContent():never{
        try{
            @readfile($this->content);
        }
        catch(Throwable){
            die("Ressource non trouvé");
        }

        die();
    }
}