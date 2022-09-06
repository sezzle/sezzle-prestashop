<?php

namespace Sezzle\HttpClient;

class RequestException extends \Exception
{
    /**
     * @var string
     */
    private $body;

    /**
     * @param string     $message
     * @param int        $code
     * @param \Exception $previous
     * @param string     $body
     */
    public function __construct($message = '', $code = 0, \Exception $previous = null, $body = null)
    {
        $this->body = $body;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}
