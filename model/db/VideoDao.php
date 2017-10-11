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
        $video = new Video($videoID, $result['title'], $result['description'], $result['date_added'], $result['uploader_id'], $result['video_url']);
        $video->setHidden($result['hidden']);
        return $video;
    }

    public function getNRandomVideos($numberOfVideos){
        $statement = $this->pdo->prepare("SELECT id, title, description, date_added, uploader_id, video_url FROM videos WHERE hidden=0 ORDER BY RAND() LIMIT ?");
        $statement->execute(array($numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosArray = array();
        foreach ($result as $key=>$value) {
            $videosArray[] = new Video($result[$key]['id'], $result[$key]['title'], $result[$key]['description'], $result[$key]['date_added'], $result[$key]['uploader_id'], $result[$key]['video_url']);
        }
        return $videosArray;
    }

    public function getNRandomVideosByTagID($numberOfVideos, $tagID){
        $statement = $this->pdo->prepare("SELECT id, title, description, date_added, uploader_id, video_url FROM videos WHERE id IN (SELECT video_id FROM tags_videos WHERE tag_id = ?) ORDER BY RAND() LIMIT ?");
        $statement->execute(array($tagID, $numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosArray = array();
        foreach ($result as $key=>$value) {
            $videosArray[] = new Video($result[$key]['id'], $result[$key]['title'], $result[$key]['description'], $result[$key]['date_added'], $result[$key]['uploader_id'], $result[$key]['video_url']);
        }
        return $videosArray;
    }

    public function getNLatestVideosByUploaderID($numberOfVideos, $uploaderID){
        $statement = $this->pdo->prepare("SELECT id, title, description, date_added, uploader_id, video_url FROM videos WHERE uploader_id=? ORDER BY date_added DESC LIMIT ?");
        $statement->execute(array($uploaderID, $numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosArray = array();
        foreach ($result as $key=>$value) {
            $videosArray[] = new Video($result[$key]['id'], $result[$key]['title'], $result[$key]['description'], $result[$key]['date_added'], $result[$key]['uploader_id'], $result[$key]['video_url']);
        }
        return $videosArray;
    }

    public function getVideoNameSuggestionsForSearch($partOfVideoName) {
        $statement = $this->pdo->prepare("SELECT title FROM videos WHERE title LIKE '%?%' LIMIT 5");
        $statement->execute(array($partOfVideoName));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosNamesArray = array();
        foreach ($result as $key=>$value) {
            $videosNamesArray[] = $result[$key]['title'];
        }
        return $videosNamesArray;
    }

    public function getNVideosByName($videoName, $numberOfVideos) {
        $statement = $this->pdo->prepare("SELECT id, title, description, date_added, uploader_id, video_url FROM videos WHERE title LIKE '%?%' ORDER BY date_added DESC LIMIT ?");
        $statement->execute(array($videoName, $numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosArray = array();
        foreach ($result as $key=>$value) {
            $videosArray[] = new Video($result[$key]['id'], $result[$key]['title'], $result[$key]['description'], $result[$key]['date_added'], $result[$key]['uploader_id'], $result[$key]['video_url']);
        }
        return $videosArray;
    }

    public function isVideoLikedOrDislikedByUser($videoID, $userID) {
        $statement = $this->pdo->prepare("SELECT likes FROM videos_likes_dislikes WHERE video_id = ? AND user_id = ?");
        $statement->execute(array($videoID, $userID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (isset($result['likes'])) {
            if ($result['likes'] == 1) {
                return true;
            }
            elseif ($result['likes'] == 0) {
                return false;
            }
            else {
                return null;
            }
        }
        else {
            return null;
        }
    }


    public function likeComment($videoID, $userID) {
        $likes = $this->isVideoLikedOrDislikedByUser($videoID, $userID);

        //if video is not liked on pressing button 'like' like is added
        if ($likes == null) {
            $statement = $this->pdo->prepare("INSERT INTO videos_likes_dislikes (video_id, user_id, likes) VALUES (?, ?, 1)");
            $statement->execute(array($videoID, $userID));
        }
        elseif ($likes == true) {
            //if already liked on pressing button 'like' again the like is removed
            $statement = $this->pdo->prepare("DELETE FROM videos_likes_dislikes WHERE video_id = ? AND user_id = ?");
            $statement->execute(array($videoID, $userID));
        }
    }


    public function dislikeComment($videoID, $userID) {
        $likes = $this->isVideoLikedOrDislikedByUser($videoID, $userID);

        //if comment is not disliked on pressing button 'dislike' dislike is added
        if ($likes == null) {
            $statement = $this->pdo->prepare("INSERT INTO videos_likes_dislikes (video_id, user_id, likes) VALUES (?, ?, 0)");
            $statement->execute(array($videoID, $userID));

        }
        elseif ($likes == false) {
            //if already disliked on pressing button 'dislike' again the dislike is removed
            $statement = $this->pdo->prepare("DELETE FROM videos_likes_dislikes WHERE video_id = ? AND user_id = ?");
            $statement->execute(array($videoID, $userID));

        }
    }

    public function getVideoLikesCountById($videoID) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as likes_count FROM videos_likes_dislikes WHERE video_id = ? AND likes = 1");
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['likes_count'];
    }

    public function getVideoDislikesCountById($videoID) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as dislikes_count FROM videos_likes_dislikes WHERE video_id = ? AND likes = 0");
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['dislikes_count'];
    }

}