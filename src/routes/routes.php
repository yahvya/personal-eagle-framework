<?php

use SaboCore\Routing\Routes\RouteManager;

// enregistrement des routes
RouteManager::fromFile(filename: "api");
RouteManager::fromFile(filename: "web");
