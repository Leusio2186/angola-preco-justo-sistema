<?php

include_once __DIR__ . "/../vendor/autoload.php";

use App\Controllers\HomeController;
use App\Core\Router;


$router = new Router();

$router->get("/", [HomeController::class, "index"]);

$router->get("/ola", [HomeController::class, "ola"]);
$router->post("/login", [HomeController::class, "login"]);



$router->dispatch();
