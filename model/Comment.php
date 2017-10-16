<?php

namespace model;


class Comment {
    private $id;
    private $videoId;
    private $userId;
    private $text;
    private $dateAdded;
    private $creatorUsername;

    /**
     * Comment constructor.
     */
    public function __construct($videoId, $userId, $text, $dateAdded, $creatorName = null) {
        $this->videoId = $videoId;
        $this->userId = $userId;
        $this->text = $text;
        $this->dateAdded = $dateAdded;
        $this->creatorUsername = $creatorName;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getVideoId()
    {
        return $this->videoId;
    }

    /**
     * @param mixed $videoId
     */
    public function setVideoId($videoId)
    {
        $this->videoId = $videoId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return mixed
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @param mixed $dateAdded
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;
    }

    /**
     * @return mixed
     */
    public function getCreatorUsername()
    {
        return $this->creatorUsername;
    }

    /**
     * @param mixed $creatorUsername
     */
    public function setCreatorUsername($creatorUsername)
    {
        $this->creatorUsername = $creatorUsername;
    }


}