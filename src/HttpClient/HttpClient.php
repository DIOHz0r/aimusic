<?php


namespace App\HttpClient;

use App\HttpClient\Handler\ErrorHandler;
use GuzzleHttp\Client as GuzzleClient;

class HttpClient
{

    protected $client;

    /**
     * @var $options
     */
    protected $options = [];

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge(
            $this->options,
            //array('message_factory' => new MessageFactory()),
            $options
        );
        $this->client = new GuzzleClient($this->options);
        $this->errorHandler = new ErrorHandler($this->options);
    }

    public function get($path, array $body = [], array $headers = [])
    {
        return $this->request($path, $body, 'GET', $headers);
    }

    public function post($path, array $body = [], array $headers = [])
    {
        return $this->request($path, $body, 'POST', $headers);
    }

    public function request($path, $body, $httpMethod = 'GET', array $headers = [])
    {
        if (!empty($this->options['debug'])) {
            $options['debug'] = $this->options['debug'];
        }
        if (count($headers) > 0) {
            $options['headers'] = $headers;
        }
        $args = array_merge($options, $body);

        try {
            $response = $this->client->request($httpMethod, $path, $args);
        } catch (\Exception $e) {
            $data = $this->errorHandler->onException($e);
            if (is_null($data)){
                echo 'Ups, a problem happened dealing with the request';
                die;
            }
            return $data;
        }

        return $response;
    }

    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }
}