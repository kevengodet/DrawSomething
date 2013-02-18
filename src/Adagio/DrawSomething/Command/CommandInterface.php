<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Client;

interface CommandInterface
{
    function execute(Client $client);
}