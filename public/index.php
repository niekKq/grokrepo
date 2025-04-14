<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Framework\Http\ServerRequest;
use Framework\Kernel\Kernel;

// Maak een ServerRequest vanuit superglobals
$request = ServerRequest::fromSuperglobals(); // hier de globals roepen om te gebruiken

$app = new Kernel();
$response = $app->handle($request);

// Stuur de response naar de browser
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("$name: $value", false);
    }
}
http_response_code($response->getStatusCode());
echo $response->getBody();