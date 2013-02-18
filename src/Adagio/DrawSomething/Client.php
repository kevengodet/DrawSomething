<?php

namespace Adagio\DrawSomething;

use Adagio\DrawSomething\Transport\Curl;
use Adagio\DrawSomething\Transport\Response;
use Adagio\DrawSomething\Entity\Factory;
use Adagio\DrawSomething\Command\CommandInterface;
use Adagio\DrawSomething\Command\ConnectedCommandInterface;
use Adagio\DrawSomething\Command\SignedCommandInterface;
use Adagio\DrawSomething\Exception\CommandException;
use Adagio\DrawSomething\Exception\ProtocolException;

/**
 * Port of the idea of Bouke van der Bijl's drawsomething-api (Python)
 *
 * @see https://github.com/boukevanderbijl/drawsomething-api
 * 
 * @method getPlayer($playerId)
 */
class Client
{
    /**
     *
     * @var string
     */
    private $deviceId;

    /**
     *
     * @var string
     */
    private $signature;

    /**
     *
     * @var string
     */
    private $userId;

    /**
     *
     * @var string
     */
    private $sessionId;

    /**
     *
     * @var Psr\Http\HttpClient
     */
    private $transport = null;

    /**
     *
     * @var Factory
     */
    private $factory;

    /**
     *
     * @var string
     */
    private $baseUrl;

    /**
     * 
     * @param string $deviceId
     * @param string $signature
     * @param \Psr\Http\HttpClient $transport
     * @param Adagio\DrawSomething\Entity\Factory $factory
     * @param string $baseUrl
     */
    public function __construct($deviceId, $signature, $transport = null, $factory = null, $baseUrl = 'http://omgpop.com/mobile_controller')
    {
        $this->deviceId  = $deviceId;
        $this->signature = $signature;

        $this->transport = $transport ? $transport : new Curl();
        $this->factory = $factory ? $factory : new Factory();

        $this->baseUrl = $baseUrl;
    }

    /**
     * 
     * @param string $userId
     * @param string $sessionId
     */
    public function reloadSession($userId, $sessionId)
    {
        $this->userId    = $userId;
        $this->sessionId = $sessionId;        
    }

    /**
     * 
     * @param string $email
     * @param string $password
     * @throws \RuntimeException
     */
    public function connect($email, $password)
    {
        $connection = $this->login($email, $password);

        $this->userId = $connection['user_id'];
        $this->sessionId = $connection['session_id'];
    }

    /**
     * 
     * @param string $name
     * @param type $parameters
     * @return CommandInterface
     * @throws CommandException
     */
    public function createCommand($name, $parameters = array())
    {
        if (!class_exists($name)) {
            if (!class_exists('\\Adagio\\DrawSomething\\Command\\'.$name)) {
                throw new CommandException("Command `$name` is unknown");
            }

            $name = '\\Adagio\\DrawSomething\\Command\\'.$name;
        }

        $r = new \ReflectionClass($name);

        /* @var $command CommandInterface */
        $command = $r->newInstanceArgs($parameters);

        if ($command instanceof ConnectedCommandInterface) {
            $command->setParameter('session_id', $this->sessionId);
        }

        if ($command instanceof SignedCommandInterface) {
            $command->setParameter('device_id', $this->deviceId);
            $command->setParameter('signature', $this->signature);
        }

        return $command;
    }

    /**
     * 
     * @param \Adagio\DrawSomething\CommandInterface $command
     */
    public function executeCommand(CommandInterface $command)
    {
        return $command->execute($this);
    }

    /**
     * 
     * @return \Adagio\DrawSomething\Entity\Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * 
     * @param string $userId
     * @return array
     * @throws \Exception
     */
    public function setUserInfo($infos, $userId = null)
    {
        if (!$this->sessionId) {
            throw new \Exception('You are not connected yet.');
        }

        if (is_null($userId)) {
            $userId = $this->userId;
        }

        $oldInfos = $this->getUserInfo($userId);
        foreach ($infos as $key => $value) {
            $oldInfos[$key] = $value;
        }

        return $this->cas("$userId:user", $oldInfos);
    }

    /**
     * 
     * @param stirng $userId
     * @return array
     * @throws \Exception
     */
    public function getUserGameList($userId = null)
    {
        if (!$this->sessionId) {
            throw new \Exception('You are not connected yet.');
        }

        if (is_null($userId)) {
            $userId = $this->userId;
        }

        return $this->get(sprintf('http://omgpop.com/mobile_controller/blob/car?app_name=drawmythingmobile&sku=paid&key=%s:gamelist&session_id=%s',
                $userId,
                $this->sessionId));
    }


    /**
     * 
     * @param \Adagio\DrawSomething\Transport\Response $response
     * @return boolean
     */
    private function isErrorResponse(Response $response)
    {
        if (array_key_exists('error_msg', json_decode($response->getContent()))) {
            return true;
        }

        return false;
    }

    /**
     * 
     * @return boolean
     */
    public function isConnected()
    {
        return !is_null($this->sessionId);
    }

    /**
     * 
     * @param string $method
     * @param string $url
     * @param array|string $content
     * @param boolean $returnResponse
     * @return string
     * @throws ProtocolException
     */
    public function request($method, $url, $content = null, $returnResponse = false)
    {
        $response = $this->transport->request($method, $this->baseUrl.$url, $content);

        if (200 !== $response->getStatusCode()) {
            throw new ProtocolException(
                    'Status code: '.$response->getStatusCode().
                    "\nContent: ".$response->getContent().
                    "\nHeaders:\n  ".implode("\n  ", $response->getHeaders()));
        }

        if (!is_null($response->getHeader('Error_code'))) {
            throw new ProtocolException($response->getHeader('Error_code'));
        }

        return $returnResponse ? $response : $response->getContent();
    }

    /**
     * 
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $command = $this->createCommand(ucfirst($name), $arguments);

        return $this->executeCommand($command);
    }
}