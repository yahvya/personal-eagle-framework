<?php

namespace SaboCore\Routing\Application;

/**
 * @brief Enumération des étapes du cycle de vie
 * @attention Les numéros associés aux évènements sont affectés par ordre d'évènement
 */
enum ApplicationCycle:int{
    /**
     * @brief Erreur durant le cycle
     */
    case ERROR_IN_CYCLE = 0;

    /**
     * @brief Quand l'URL est redirigé sur le point d'entrée.
     */
    case INIT = 1;

    /**
     * @brief Après le chargement des configurations
     */
    case CONFIG_LOADED = 2;

    /**
     * @brief Avant le chargement de la base de données
     */
    case BEFORE_DATABASE_INIT = 3;

    /**
     * @brief Après le chargement de la base de données
     */
    case AFTER_DATABASE_INIT = 4;

    /**
     * @brief Lancement du routage des controllers
     */
    case START_ROUTING = 5;

    /**
     * @brief Gestion de l'état de maintenance, fourni l'instance du RoutingManager
     */
    case CHECK_MAINTENANCE = 6;

    /**
     * @brief Gestion de la demande en cas de ressources, fourni l'instance du RoutingManager
     */
    case CHECK_RESOURCE_REQUIRED = 7;

    /**
     * @brief Route requise trouvée, fourni (RoutingManager,array $searchResults)
     * @param array $searchResults au format ["route" => Route,"match" => MatchResult]
     */
    case ROUTE_FOUNDED = 8;

    /**
     * @brief Condition d'accès à la route non vérifiée, fourni (RoutingManager, Verifier)
     */
    case ROUTE_VERIFIER_FAILED = 9;

    /**
     * @brief Affichage de la réponse, fourni (RoutingManager, Callable, mixed $args), le controller ou fonction Callabe lié à la route, arguments fournis à la fonction
     */
    case RENDER_RESPONSE = 10;
}
