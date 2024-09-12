<?php

use SaboCore\Routing\Request\Request;
use SaboCore\Routing\Response\JsonResponse;
use SaboCore\Routing\Routes\Route;

Route::get(
    link: "/:username",
    executor: fn(Request $request,string $username):JsonResponse => new JsonResponse(json: ["message" => "Welcome to <Sabo framework>"]),
    routeName: "sabo.welcome"
);