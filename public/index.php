<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("<h1>Home page!</h1>");
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("<h1>Hello, $name!</h1>");
    return $response;
});

$app->get('/load', function (Request $request, Response $response, array $args) {
    $time_start = microtime(true);

    $a = 0;
    for ($i = 0; $i < 100000000; $i++) {
        $a += $i;
    }

    $time_end = microtime(true);

    $total_time = round($time_end - $time_start, 4);

    $response->getBody()->write("<h1>Page generated in '$total_time' seconds.</h1>");
    return $response;
});

$app->get('/hc', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("");
    return $response;
});

$app->run();
