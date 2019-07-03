<?php

namespace Api\Responses;

class Response
{
    protected $headers;

    protected $content;

    /**
     * Response constructor.
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->headers = new Headers();
        $this->setContent($content);
    }

    /**
     * @param $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return $this
     */
    protected function prepare()
    {
        $this->headers->send();

        return $this;
    }

    /**
     * @return $this
     */
    public function send()
    {
        $this->prepare();

        echo $this->content;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        $this->prepare();

        return $this->content;
    }
}
