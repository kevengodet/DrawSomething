<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Client;
use Adagio\DrawSomething\Exception\CommandException;

class BlobRevision extends Command implements ConnectedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/blob/car';
    /**
     * 
     * @param string $key
     */
    public function __construct($key)
    {
        $this->setParameter('key', $key);
    }

    /**
     * 
     * @param Client $client
     * @return string
     */
    public function execute(Client $client)
    {
        if ($this instanceof ConnectedCommandInterface && !$client->isConnected()) {
            throw new CommandException(get_class($this).' command requires the client to be connected.');
        }

        $response = $client->request('HEAD', $this->path.'?'.http_build_query($this->parameters), null, true);

        return $response->getHeader('Blob-Revision');
    }
}