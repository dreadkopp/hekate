<?php

// Get the requested URI
$requestedRoute = $_SERVER['REQUEST_URI'];

// Return the requested route as a plain text response
header('Content-Type: text/plain');
echo 'Requested Route: ' . $requestedRoute . PHP_EOL;
echo 'Headers: ' . PHP_EOL;
$headers = getallheaders();
foreach ($headers as $header => $value) {
    echo "$header: $value" .PHP_EOL;
}
