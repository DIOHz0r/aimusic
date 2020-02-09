<?php

namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class DefaultController
{

    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function albums(Request $request, Response $response)
    {
        $q = $request->getParam('q');
        if (!$q) {
            return $response->withStatus(400)
                ->withJson(['error' => 'Query argument not defined, please use /albums?q=band-name']);
        }

        $spotifySession = $_SESSION['spotifySession'];
        $httpclient = $this->container->get('httpclient');
        $headerRequest = ['Authorization' => $spotifySession['token_type'] . ' ' . $spotifySession['access_token']];
        $result = $httpclient->get('search', ['query' => ['q' => $q, 'type' => 'artist', 'limit' => 1]],
            $headerRequest);

        $statusCode = $result->getStatusCode();
        if ($statusCode != 200) {
            return $response->withStatus($statusCode)
                ->withHeader('content-type','application/json')
                ->withBody($result->getBody());
        }

        $data = json_decode($result->getBody()->getContents(), true);
        $artistId = $data['artists']['items'][0]['id'];
        $result = $httpclient->get('artists/' . $artistId . '/albums', ['query' => ['market' => 'AR']], $headerRequest);

        $statusCode = $result->getStatusCode();
        if ($statusCode != 200) {
            return $response->withStatus($statusCode)
                ->withHeader('content-type','application/json')
                ->withBody($result->getBody());
        }

        $data = json_decode($result->getBody()->getContents(), true);
        $final = [];
        foreach ($data['items'] as $item) {
            $final[] = [
                'name' => $item['name'],
                'released' => $item['release_date'],
                'tracks' => $item['total_tracks'],
                'cover' => [
                    'height' => $item['images'][0]['height'],
                    'width' => $item['images'][0]['width'],
                    'url' => $item['images'][0]['url'],
                ],
            ];
        }
        return $response->withJson($final);
    }
}