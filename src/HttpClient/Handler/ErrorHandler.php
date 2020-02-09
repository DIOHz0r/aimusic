<?php


namespace App\HttpClient\Handler;

use App\HttpClient\Exception\ConnectException;
use App\HttpClient\Exception\LogicException;
use App\HttpClient\Exception\RuntimeException;
use GuzzleHttp\Exception\ConnectException as GuzzleConnectException;
use GuzzleHttp\Exception\RequestException;

class ErrorHandler
{
    /**
     * Handler options.
     *
     * @var array
     */
    private $options;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Handles different types of exceptions.
     *
     * @param \Exception $e The exception.
     * @return \Psr\Http\Message\ResponseInterface|null
     * @throws ConnectException
     * @throws LogicException
     * @throws RuntimeException
     */
    public function onException(\Exception $e)
    {
        if ($e instanceOf GuzzleConnectException) {
            throw new ConnectException($e->getMessage());
        }
        if ($e instanceOf RequestException) {
            $response = $e->getResponse();
            if (!$response) {
                throw new RuntimeException($e->getMessage(), $e->getCode());
            }
            return $response;
        }
        if ($e instanceOf \LogicException) {
            throw new LogicException($e->getMessage(), $e->getCode());
        }
        throw new RuntimeException($e->getMessage(), $e->getCode());
    }

}