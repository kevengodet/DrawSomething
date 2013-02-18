<?php

namespace Adagio\DrawSomething\Command;

class Login extends Command implements CommandInterface, SignedCommandInterface
{
    /**
     *
     * @var string 
     */
    protected $path = '/login';

    /**
     * 
     * @param string $emailAddress
     * @param string $password
     */
    public function __construct($emailAddress, $password)
    {
        $this->setParameter('email_address', $emailAddress);
        $this->setParameter('password', $password);
    }
}