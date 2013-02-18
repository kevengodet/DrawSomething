<?php

namespace Adagio\DrawSomething\Entity;

class Player extends Entity
{
    private $id;
    private $username;
    private $facebookId;
    private $facebookName;
    private $lastLoginTime;
    private $turnCounter;
    private $locale;
    private $wordLists = array();
    private $mobileUserId;

    /**
     * 
     * @param int $id
     * @param string $username
     * @param int $facebookId
     * @param string $facebookName
     * @param int $lastLoginTime
     * @param int $turnCounter
     * @param string $locale
     * @param array $wordLists
     * @param ssint $mobileUserId
     */
    public function __construct($id = null, $username = null, $facebookId = null, $facebookName = null, $lastLoginTime = null, $turnCounter = null, $locale = null, $wordLists = array(), $mobileUserId = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->facebookId = $facebookId;
        $this->facebookName = $facebookName;
        $this->lastLoginTime = $lastLoginTime;
        $this->turnCounter = $turnCounter;
        $this->locale = $locale;
        $this->wordLists = $wordLists;
        $this->mobileUserId = $mobileUserId;
    }

    public function getId()
    {
        return $this->id ? $this->id : $this->mobileUserId;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function getFacebookName()
    {
        return $this->facebookName;
    }

    public function getName()
    {
        return $this->facebookName ? $this->facebookName : $this->username;
    }

    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    public function getTurnCounter()
    {
        return $this->turnCounter;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function getWordLists()
    {
        return $this->wordLists;
    }
}