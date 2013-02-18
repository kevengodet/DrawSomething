<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Entity\Player;

class Inventory extends Command implements ConnectedCommandInterface, SignedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/inventory';
}