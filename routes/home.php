<?php

use Controllers\TestController;
use SaboCore\Routing\Routes\Route;

Route::get(
    link: "/:username",
    executor: [TestController::class, "execute"],
    routeName: "sabo.welcome"
);