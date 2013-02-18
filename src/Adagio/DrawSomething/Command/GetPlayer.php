<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Entity\Player;

class GetPlayer extends Read implements ConnectedCommandInterface
{
    /**
     * 
     * @param string|Player $userId
     */
    public function __construct($userId)
    {
        if ($userId instanceof Player) {
            $userId = $userId->getId();
        }

        parent::__construct("$userId:user");
    }

    /**
     * 
     * @param string $data
     * @param \Adagio\DrawSomething\Entity\Factory $factory
     * @return \Adagio\DrawSomething\Entity\Player
     */
    public function process($data, $factory = null)
    {
        return $factory->build('Player', parent::process($data));
    }
}