<?php

namespace Adagio\DrawSomething\Command;

use Adagio\DrawSomething\Client;
use Adagio\DrawSomething\Exception\CommandException;

abstract class Command
{
    /**
     *
     * @var string
     */
    protected $path;

    /**
     *
     * @var array
     */
    protected $parameters =  array(
                'app_name' => 'drawmythingmobile',
                'sku'      => 'paid',
            );

    /**
     * 
     * @param string $name
     * @param mixed $value
     * @return CommandInterface
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;

        return $this;
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

        $response = $this->process(
                $client->request('GET', $this->path.'?'.http_build_query($this->parameters)),
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

    /**
     * 
     * @param string $data
     * @param \Adagio\DrawSomething\Entity\Factory $factory
     * @return array
     */
    protected function process($data, $factory = null)
    {
        return (array) json_decode($data);
    }
}