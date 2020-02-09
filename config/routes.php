<?php

use App\Controller\DefaultController;
use Slim\App;

return function (App $app) {
    $app->get('/api/v1/albums', DefaultController::class . ':albums');
};