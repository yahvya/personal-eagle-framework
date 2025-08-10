<?php

use Yahvya\EagleFramework\Routing\Response\BladeResponse;
use Yahvya\EagleFramework\Routing\Routes\RouteManager;

// WEB routes

RouteManager::registerRoute(
    requestMethod: "GET",
    link: "/",
    toExecute: fn(): BladeResponse => new BladeResponse(
        pathFromViews: "eagle",
        datas: ["websiteLink" => "https://yahvya.github.io/personal-sabo-framework-doc/starter-topic.html"]
    ),
    routeName: "eagle"
);
