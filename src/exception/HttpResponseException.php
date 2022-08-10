<?php

namespace ExAdmin\hyperf\exception;





use Hyperf\HttpMessage\Server\Response;

class HttpResponseException extends \RuntimeException
{
    /**
     * The underlying response instance.
     *
     * @var Response
     */
    protected $response;

    /**
     * Create a new HTTP response exception instance.
     *
     * @param Response $response
     * @return void
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get the underlying response instance.
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}