<?php

namespace Adagio\DrawSomething\Transport;

class Response // implements Psr\Http\Client\Response
{
    /**
     *
     * @var int
     */
    private $statusCode;

    /**
     *
     * @var string
     */
    private $contentType;

    /**
     *
     * @var string
     */
    private $content;

    /**
     *
     * @var array
     */
    private $headers = array();

    /**
     * 
     * @param int $statusCode
     * @param string $contentType
     * @param string $content
     * @param array $headers
     */
    public function __construct($statusCode, $contentType, $content, $headers)
    {
        $this->statusCode = $statusCode;
        $this->contentType = $contentType;
        $this->content = $content;
        $this->headers = $headers;
    }

    /**
     * 
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * 
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * 
     * @param string $name
     * @return string
     */
    public function getHeader($name)
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
    }
}