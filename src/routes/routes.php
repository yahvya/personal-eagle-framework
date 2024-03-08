<?php

use SaboCore\Routing\Routes\RouteManager;

// enregistrement des routes
RouteManager::fromFile("api");
RouteManager::fromFile("web");
