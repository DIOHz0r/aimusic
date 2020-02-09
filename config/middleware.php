<?php

use App\Middleware\SpotifyAuth;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();
    $httpClient = $container->get('httpclient');
    $options = $container->get('settings')['spotify'];
    $app->add(new SpotifyAuth($httpClient, $options));
};