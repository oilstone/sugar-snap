<?php

namespace Api\Responses;

class Json extends Response
{
    /**
     * @return Response
     */
    protected function prepare()
    {
        $this->headers->contentTypeJson();

        return parent::prepare();
    }
}
