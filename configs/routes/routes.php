<?php

use Yahvya\EagleFramework\Routing\Routes\RouteManager;

// Route initial registration
RouteManager::fromFile(filename: "api");
RouteManager::fromFile(filename: "web");
