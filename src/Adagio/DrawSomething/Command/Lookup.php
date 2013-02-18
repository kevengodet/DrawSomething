<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Entity\Player;

class Lookup extends Command implements ConnectedCommandInterface, SignedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/mobile_user_lookup';

    /**
     *
     * @var array Lookup types
     */
    protected $types = array(
        'username',
        'email',
        'facebook_id',
    );

    /**
     * 
     * @param string $username
     */
    public function __construct($username, $type = 'username')
    {
        if (!in_array($type, $this->types)) {
            throw new CommandException('Lookup type must be one in: '.implode(', ', $this->types));
        }

        $this->setParameter('lookup_id', $username);
        $this->setParameter('type', $type);
    }

    public function execute(\Adagio\DrawSomething\Client $client)
    {
        $data = parent::execute($client);

        if (!$data['success']) {
            return false;
        }

        return $client->getPlayer($data['mobile_user_id']);
    }
}