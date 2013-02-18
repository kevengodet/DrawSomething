<?php

namespace Adagio\DrawSomething\Entity;

use Adagio\DrawSomething\Client;

interface ClientAwareInterface
{
    /**
     * 
     * @param \Adagio\DrawSomething\Client $client
     */
    function setClient(Client $client);
}