<?php

namespace Adagio\DrawSomething\Command;

class Read extends Command implements ConnectedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/blob/car';

    /**
     * 
     * @param string $key
     * @param int $revision
     */
    public function __construct($key, $revision = null)
    {
        $this->setParameter('key', $key);
        if ($revision) {
            $this->setParameter('revision', $revision);
        }
    }
}