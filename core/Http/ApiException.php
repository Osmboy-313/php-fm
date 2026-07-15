<?php

// The only OOP code in my entire procedural project!

class ApiException extends Exception
{
    protected array $response;

    public function __construct(array $response)
    {
        parent::__construct();
        $this->response = $response;
    }

    public function getResponse(): array
    {
        return $this->response;
    }
}

?>