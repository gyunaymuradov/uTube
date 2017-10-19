<?php
namespace model;
class Video implements \JsonSerializable {
    private $id;
    private $title;
    private $description;
    private $dateAdded;
    private $uploaderID;
    private $videoURL;
    private $thumbnailURL;
    private $hidden;
    private $tagId;
    /**
     * Video constructor.
     * @param $id
     * @param $title
     * @param $description
     * @param $dateAdded
     * @param $uploaderID
     * @param $videoURL
     * @param $thumbnailURL
     * @param $tags
     */
    public function __construct($id, $title, $description, $dateAdded, $uploaderID, $videoURL, $thumbnailURL, $tagId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->dateAdded = $dateAdded;
        $this->uploaderID = $uploaderID;
        $this->videoURL = $videoURL;
        $this->thumbnailURL = $thumbnailURL;
        $this->hidden = 0;
        $this->tagId = $tagId;
    }

    public function jsonSerialize()
    {
        $vars = get_object_vars($this);

        return $vars;
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
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
    public function getUploaderID()
    {
        return $this->uploaderID;
    }
    /**
     * @param mixed $uploaderID
     */
    public function setUploaderID($uploaderID)
    {
        $this->uploaderID = $uploaderID;
    }
    /**
     * @return mixed
     */
    public function getVideoURL()
    {
        return $this->videoURL;
    }
    /**
     * @param mixed $videoURL
     */
    public function setVideoURL($videoURL)
    {
        $this->videoURL = $videoURL;
    }
    /**
     * @return mixed
     */
    public function getHidden()
    {
        return $this->hidden;
    }
    /**
     * @param mixed $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }
    /**
     * @return mixed
     */
    public function getTagId()
    {
        return $this->tagId;
    }
    /**
     * @param int $tagId
     */
    public function setTagId($tagId)
    {
        $this->tagId = $tagId;
    }

    /**
     * @return mixed
     */
    public function getThumbnailURL()
    {
        return $this->thumbnailURL;
    }

    /**
     * @param mixed $thumbnailURL
     */
    public function setThumbnailURL($thumbnailURL)
    {
        $this->thumbnailURL = $thumbnailURL;
    }


}