<?php

use SaboCore\Routing\Response\JsonResponse;
use SaboCore\Routing\Routes\Route;

Route::get(
    link: "/",
    executor: fn():JsonResponse => new JsonResponse(json: ["message" => "Welcome to <Sabo framework>"]),
    routeName: "sabo.welcome"
);