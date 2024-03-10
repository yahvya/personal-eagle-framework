<?php

namespace SaboCore\Utils\Mailer;

use Throwable;

/**
 * @brief Représente un fournisseur html pour mail à partir d'un template
 * @author yahaya bathily https://github.com/yahvya
 */
abstract class MailerTemplateProvider{
    /**
     * @var string Chemin du template à partir du dossier views
     */
    protected string $templatePath;

    /**
     * @var string Contenu alternatif au html
     */
    protected string $altContent;

    /**
     * @var array Données du template
     */
    protected array $templateDatas;

    /**
     * @param string $templatePath Chemin du template à partir du dossier
     * @param string $altContent Le contenu alternatif au html
     * @param array $templateDatas Données à fournir au template
     */
    public function __construct(string $templatePath,string $altContent,array $templateDatas = []){
        $this->templatePath = $templatePath;
        $this->templateDatas = $templateDatas;
        $this->altContent = $altContent;
    }

    /**
     * @return string le contenu alternatif au html
     */
    public function getAltContent():string{
        return $this->altContent;
    }

    /**
     * @return string le contenu du template construit
     * @throws Throwable en cas d'erreur
     */
    abstract function buildContent():string;
}