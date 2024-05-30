<?php

namespace SaboCore\Routing\Response;

use Override;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

/**
 * @brief Réponse ressource
 * @author yahaya bathily https://github.com/yahvya
 */
class ResourceResponse extends Response{
    /**
     * @param string $ressourceAbsolutePath chemin absolu du fichier à fournir
     * @attention le fichier fourni doit exister
     */
    public function __construct(string $ressourceAbsolutePath){
        $this->content = $ressourceAbsolutePath;

        try{
            $fileExtension = @pathinfo(path: $ressourceAbsolutePath,flags: PATHINFO_EXTENSION);

            $this->setHeader(name: "Content-Type",value: (new MimeTypes)->getMimeTypes($fileExtension)[0]);
        }
        catch(Throwable){}
    }

    #[Override]
    protected function renderContent(): never{
        try{
            @readfile(filename: $this->content);
        }
        catch(Throwable){
            die("Ressource non trouvé");
        }
        die();
    }
}
