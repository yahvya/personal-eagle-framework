<?php

namespace SaboCore\Controller;

use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\DownloadResponse;
use SaboCore\Routing\Response\HtmlResponse;
use SaboCore\Routing\Response\JsonResponse;
use SaboCore\Routing\Response\RedirectResponse;

/**
 * @brief controllers parent
 * @attention used as marker
 */
abstract class SaboController{
    /**
     * @param Request $request request
     */
    public function __construct(
        public readonly Request $request
    ){
    }

    /**
     * @brief build a response to download a resource
     * @param string $resourceAbsolutePath file absolute path
     * @param string|null $chosenName downloadable resource name , if null the default one will be used
     * @attention the given file must exist
     * @return DownloadResponse the created response
     */
    public function buildDownloadResponse(string $resourceAbsolutePath, ?string $chosenName = null):DownloadResponse{
        return new DownloadResponse(resourceAbsolutePath:  $resourceAbsolutePath,chosenName: $chosenName);
    }


    /**
     * @brief build an HTML response
     * @param string $content the HTML string
     * @return HtmlResponse the created response
     */
    public function buildHtmlResponse(string $content):HtmlResponse{
        return new HtmlResponse(content: $content);
    }

    /**
     * @brief build a json response
     * @param array $json json content
     * @return JsonResponse the created response
     */
    public function buildJsonResponse(array $json):JsonResponse{
        return new JsonResponse(json: $json);
    }

    /**
     * @brief build a redirection response
     * @param string $link link to redirect on
     * @attention a link is expected not a route name, use the route getting functions to build the link
     * @return RedirectResponse the created response
     */
    public function buildRedirection(string $link):RedirectResponse{
        return new RedirectResponse(link: $link);
    }
}