<?php

// HTTP configuration

routeManager()
    ->setCorsAllowedDomains(corsAllowedDomains: []);

route()
    ->setLink(link: "/")
    ->setRouteName(routeName: "sabo.framework")
    ->allowGet(handler: function():never{
        die("Framework Sabo");
    });