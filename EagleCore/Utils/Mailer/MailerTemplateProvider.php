<?php

namespace Yahvya\EagleFramework\Utils\Mailer;

use Throwable;

/**
 * @brief Represents an HTML provider for email from a template
 */
abstract class MailerTemplateProvider
{
    /**
     * @var string Path to the template from the views folder
     */
    protected string $templatePath;

    /**
     * @var string Alternative content to HTML
     */
    protected(set) string $altContent;

    /**
     * @var array Template data
     */
    protected array $templateDatas;

    /**
     * @param string $templatePath Path to the template from the folder
     * @param string $altContent Alternative content to HTML
     * @param array $templateDatas Data to provide to the template
     */
    public function __construct(string $templatePath, string $altContent, array $templateDatas = [])
    {
        $this->templatePath = $templatePath;
        $this->templateDatas = $templateDatas;
        $this->altContent = $altContent;
    }

    /**
     * @return string The built template content
     * @throws Throwable In case of error
     */
    abstract function buildContent(): string;
}
