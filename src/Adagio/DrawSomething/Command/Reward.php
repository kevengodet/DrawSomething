<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Entity\Player;
use Adagio\DrawSomething\Exception\CommandException;

class Reward extends Command implements ConnectedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/reward';

    /**
     *
     * @var array
     */
    protected $amounts = array(
        775 => 1,
        776 => 2,
        777 => 3,
        1132 => 100,
    );

    public function __construct($otherUsedId, $amount)
    {
        if ($otherUsedId instanceof Player) {
            $otherUsedId = $otherUsedId->getId();
        }

        $this->setParameter('other_user_id', $otherUsedId);
        $this->setParameter('reward_id', $amount);
    }

    /**
     * 
     * @param string $data
     * @param \Adagio\DrawSomething\Entity\Factory $factory
     * @return string
     */
    public function process($data, $factory = null) {
        $data = parent::process($data);

        return $data['gc_mdmt_hard'];
    }
}