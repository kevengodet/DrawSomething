<?php

namespace Adagio\DrawSomething\Entity;

use Adagio\DrawSomething\Client;

abstract class Entity implements ClientAwareInterface
{
    /**
     *
     * @var Client
     */
    private $client;

    /**
     * 
     * @param \Adagio\DrawSomething\Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }    
}