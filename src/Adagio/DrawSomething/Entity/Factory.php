<?php

namespace Adagio\DrawSomething\Entity;

use Adagio\DrawSomething\Client;
use Adagio\DrawSomething\Entity\ClientAwareInterface;
class Factory
{
    /**
     *
     * @var \Adagio\DrawSomething\Client
     */
    private $client;

    /**
     * 
     * @param \Adagio\DrawSomething\Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client;
    }

    /**
     * 
     * @param string $class
     * @param array $content
     * @return object
     */
    public function build($class, $content)
    {
        if (!class_exists($class)) {
            if (!class_exists('Adagio\\DrawSomething\\Entity\\'.$class)) {
                throw new \InvalidArgumentException("`$class` class is unknown to DrawSomething model.");
            }
            $class = 'Adagio\\DrawSomething\\Entity\\'.$class;
        }

        $r = new \ReflectionClass($class);
        $args = array_change_key_case($this->getConstructorParameters($r));

        // Fill parameters with actual values, when available
        foreach ($content as $key => $value) {
            $normalized = strtolower(str_replace('_', '', $key));
            if (array_key_exists($normalized, $args)) {
                $args[$normalized] = $value;
            }
        }

        $instance = $r->newInstanceArgs($args);

        if ($this->client && $instance instanceof ClientAwareInterface) {
            $instance->setClient($this->client);
        }

        return $instance;
    }

    /**
     * Build array of default parameters
     *
     * @param \ReflectionClass $class
     * @return array
     */
    private function getConstructorParameters(\ReflectionClass $class)
    {
        $args = array();
        foreach ($class->getConstructor()->getParameters() as $p) {
            $args[$p->getName()] = $p->getDefaultValue();
        }

        return $args;
    }
}