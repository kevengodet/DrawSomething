<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Client;
use Adagio\DrawSomething\Exception\CommandException;

class Set extends Command implements ConnectedCommandInterface
{
    /**
     *
     * @var string
     */
    protected $path = '/blob/cas';

    /**
     *
     * @var array
     */
    private $data;

    /**
     * 
     * @param string $key
     */
    public function __construct($key, $data, $revision = null)
    {
        $this->setParameter('key', $key);
        if ($revision) {
            $this->setParameter('revision', $revision);
        }
        $this->data = $data;
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

        if (!isset($this->parameters['revision'])) {
            $this->setParameter('revision', $client->blobRevision($this->parameters['key']));
        }

        $read = $client->read($this->parameters['key']);
        foreach ($this->data as $key => $value) {
            $read[$key] = $value;
        }

        $payload = json_encode($read);

        $response = $this->process(
                $client->request('POST', $this->path.'?'.http_build_query($this->parameters), $payload),
                $client->getFactory()
            );

        if (is_array($response) && array_key_exists('error_code', $response)) {
            throw new CommandException($response['error_code']);
        }

        if (is_array($response) && array_key_exists('error_msg', $response)) {
            throw new CommandException($response['error_msg']);
        }

        return $response;
    }
}