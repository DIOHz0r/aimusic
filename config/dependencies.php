<?php

use App\HttpClient\HttpClient;
use Slim\App;

return function (App $app) {
    $container = $app->getContainer();
    $container['httpclient'] = function ($c) {
        return new HttpClient([
            'base_uri' => 'https://api.spotify.com/v1/',
        ]);
    };
};