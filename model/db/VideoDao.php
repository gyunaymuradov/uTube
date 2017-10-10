<?php

namespace model\db;

use model\db\DBManager;
use model\Video;
use \PDO;
use \PDOException;

class VideoDao {
    private static $instance;
    private $pdo;

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new VideoDao();
        }
        return self::$instance;
    }

    public function insertVideo(Video $video, $tags) {
        try {
            $this->pdo->beginTransaction();
            $statement = $this->pdo->prepare("INSERT INTO videos (title, description, date_added, uploader_id, video_url, hidden) VALUES (?, ?, ?, ?, ?, ?)");
            $statement->execute(array($video->getTitle(), $video->getDescription(), $video->getDateAdded(), $video->getUploaderID(), $video->getVideoURL(), $video->getHidden()));
            $video->setId($this->pdo->lastInsertId());
            foreach ($tags as $tag) {
                $statement = $this->pdo->prepare("INSERT INTO tags_videos (tag_id, video_id) VALUES (?, ?)");
                $statement->execute(array($tag, $video->getId()));
            }
            $this->pdo->commit();
        }
        catch (PDOException $e) {
            if($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
    public function deleteVideo($videoID) {
        $statement = $this->pdo->prepare("UPDATE TABLE videos SET hidden=1 WHERE id = ?");
        $statement->execute(array($videoID));
    }

    public function editVideo(Video $video) {
        $statement = $this->pdo->prepare("UPDATE TABLE videos SET title=?, description=? WHERE id=?");
        $statement->execute(array($video->getTitle(), $video->getDescription(), $video->getId()));
    }

    public function getOneVideo($videoID) {
        $statement = $this->pdo->prepare("SELECT title, description, date_added, uploader_id, video_url, hidden FROM videos WHERE id=?");
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $video = new Video($result['title'], $result['description'], $result['date_added'], $result['uploader_id'], $result['video_url']);
        $video->setId($videoID);
        $video->setHidden($result['hidden']);
        return $video;
    }

    public function getNRandomVideos($numberOfVideos){
        $statement = $this->pdo->prepare("SELECT id, title, description, date_added, uploader_id, video_url, hidden FROM videos ORDER BY RAND() LIMIT ?");
        $statement->execute(array($numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        //ToDO convert result to an array of objects of type Video
    }

}