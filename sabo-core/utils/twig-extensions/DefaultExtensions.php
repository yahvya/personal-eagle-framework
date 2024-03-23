<?php

namespace SaboCore\Utils\TwigExtensions;

use SaboCore\Utils\Csrf\CsrfManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @brief Extensions par défaut de twig
 * @author yahaya bathily https://github.com/yahvya
 */
class DefaultExtensions extends AbstractExtension {
    public function getFunctions():array{
        return [
            new TwigFunction(name: "route",callable: [$this,"route"]),
            new TwigFunction(name: "generateCsrf",callable: [$this,"generateCsrf"]),
            new TwigFunction(name: "checkCsrf",callable: [$this,"checkCsrf"]),
        ];
    }

    /**
     * @brief Recherche une route pour la fournir
     * @param string $requestMethod méthode de requête
     * @param string $routeName nom de la route
     * @param array<string,string> $replaces éléments de remplacements dans le lien
     * @return string|null le lien lié à la route
     */
    public function route(string $requestMethod,string $routeName,array $replaces = []):string|null{
        return route(requestMethod: $requestMethod,routeName: $routeName,replaces: $replaces);
    }

    /**
     * @brief Génère un token csrf
     * @return CsrfManager le gestion de token
     */
    public function generateCsrf():CsrfManager{
        return generateCsrf();
    }

    /**
     * @brief Vérifie que le token soit valide et le supprime si valide
     * @param string $token
     * @return bool si le token est valide
     */
    public function checkCsrf(string $token):bool{
        return checkCsrf(token: $token);
    }
}