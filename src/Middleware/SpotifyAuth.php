<?php


namespace App\Middleware;


use App\HttpClient\HttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SpotifyAuth
{

    /**
     * @var HttpClient
     */
    private $client;
    /**
     * @var array
     */
    private $options;

    public function __construct(HttpClient $client, array $options)
    {
        $this->client = $client;
        $this->options = $options;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if (!$this->isAccessTokenExpired()) {
            return $next($request, $response);
        }

        $client = $this->client;
        $options = $this->options;

        $encode = base64_encode($options['clientId'] . ':' . $options['clientSecret']);
        $result = $client->post('https://accounts.spotify.com/api/token',
            ['form_params' => ['grant_type' => 'client_credentials']],
            ['Authorization' => 'Basic ' . $encode]
        );

        $statusCode = $result->getStatusCode();
        if ($statusCode != 200) {
            return $response->withStatus($statusCode)
                ->withHeader('content-type','application/json')
                ->withBody($result->getBody());
        }

        $data = json_decode($result->getBody()->getContents(), true);
        $currentTime = new \DateTime();
        $expires_in = $data['expires_in'] - 1;
        $currentTime->add(new \DateInterval('PT' . $expires_in . 'S'));
        $data['expiresAt'] = $currentTime->getTimestamp();
        $_SESSION['spotifySession'] = $data;

        return $next($request, $response);
    }

    private function isAccessTokenExpired()
    {
        if (!array_key_exists('spotifySession', $_SESSION)) {
            return true;
        }

        $now = new \DateTime();
        if ($now->getTimestamp() >= $_SESSION['spotifySession']['expiresAt']) {
            return true;
        }
    }
}