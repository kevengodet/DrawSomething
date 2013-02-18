<?php

namespace Adagio\DrawSomething\Command;

class GetBalance extends Command implements ConnectedCommandInterface, SignedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/currency_balance';

    /**
     * 
     * @param string $data
     * @param \Adagio\DrawSomething\Entity\Factory $factory
     * @return int
     */
    public function process($data, $factory = null) {
        $data = parent::process($data);

        return $data['gc_mdmt_hard'];
    }
}