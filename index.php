<?php

// This API uses REST web service
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\PhpRenderer;

// External requires
require 'vendor/autoload.php';
require 'includes/dbconnect.php';
require 'includes/mercadolibre.php';

$app = new \Slim\App;
$container = $app->getContainer();
$container['renderer'] = new PhpRenderer("templates/");

// $app->get is listening the url path. In this case domain.com/ match with '/'
$app->get('/', function (Request $request, Response $response, $args) {

    // Connect with Mercadolibre. Constructor print on log automatic status. If cant connect gives a link to proceed
    $ml = new Mercadolibre();

    $args = Array(
      'categorias' => $ml->getCategories() //Get Mercadolibre parent categorys
    );

    return $this->renderer->render($response, "/home.php", $args);
});

$app->run();

?>
