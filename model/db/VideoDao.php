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

    /**
     * Insert Video in DB
     * @param Video $video
     * @param array $tagIDs
     */
    public function insertVideo(Video $video, Array $tagIDs) {
        try {
            $this->pdo->beginTransaction();
            $statement = $this->pdo->prepare("INSERT INTO videos (title, description, date_added, uploader_id, video_url, hidden) VALUES (?, ?, ?, ?, ?, ?)");
            $statement->execute(array($video->getTitle(), $video->getDescription(), $video->getDateAdded(), $video->getUploaderID(), $video->getVideoURL(), $video->getHidden()));
            $video->setId($this->pdo->lastInsertId());
            foreach ($tagIDs as $tagID) {
                $statement = $this->pdo->prepare("INSERT INTO tags_videos (tag_id, video_id) VALUES (?, ?)");
                $statement->execute(array($tagID, $video->getId()));
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

    /**
     * Delete video from DB by Video id
     * @param int $videoID
     */
    public function deleteVideo($videoID) {
        $statement = $this->pdo->prepare("UPDATE TABLE videos SET hidden=1 WHERE id = ?");
        $statement->execute(array($videoID));
    }

    /**
     * Edit video in DB
     * @param Video $video
     */
    public function editVideo(Video $video) {
        $statement = $this->pdo->prepare("UPDATE TABLE videos SET title=?, description=? WHERE id=?");
        $statement->execute(array($video->getTitle(), $video->getDescription(), $video->getId()));
    }

    /**
     * Return one Video by Video id
     * @param int $videoID
     * @return Video
     */
    public function getOneVideo($videoID) {
        $statement = $this->pdo->prepare("SELECT title, description, date_added, uploader_id, video_url, hidden FROM videos WHERE id=?");
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        $video = new Video($videoID, $result['title'], $result['description'], $result['date_added'], $result['uploader_id'], $result['video_url']);
        $video->setHidden($result['hidden']);
        return $video;
    }

    /**
     * Return array of N random Videos
     * @param int $numberOfVideos
     * @return array
     */
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

    /**
     * Return array of N random Videos by Tag id
     * @param int $numberOfVideos
     * @param int $tagID
     * @return array
     */
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

    /**
     * Return array of N Videos by Uploader id
     * @param int $numberOfVideos
     * @param int $uploaderID
     * @return array
     */
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

    /**
     * Return array of 5 Videos by title
     * For search box suggestions
     * @param string $partOfVideoName
     * @return array
     */
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

    /**
     * Return array of N Videos by Video name
     * @param string $videoName
     * @param int $numberOfVideos
     * @return array
     */
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

    /**
     * Return true if Video is liked by User
     * Return false if Video is disliked by User
     * Return null if Video is not liked or disliked by User
     * @param int $videoID
     * @param int $userID
     * @return bool|null
     */
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

    /**
     * User with userID Likes video with videoID
     * @param int $videoID
     * @param int $userID
     */
    public function likeVideo($videoID, $userID) {
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

    /**
     * User with userID Dislikes video with videoID
     * @param int $videoID
     * @param int $userID
     */
    public function dislikeVideo($videoID, $userID) {
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

    /**
     * Get number of likes on Video by Video id
     * @param $videoID
     * @return mixed
     */
    public function getVideoLikesCountById($videoID) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as likes_count FROM videos_likes_dislikes WHERE video_id = ? AND likes = 1");
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['likes_count'];
    }

    /**
     * Get number of dislikes on Video by Video id
     * @param $videoID
     * @return mixed
     */
    public function getVideoDislikesCountById($videoID) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as dislikes_count FROM videos_likes_dislikes WHERE video_id = ? AND likes = 0");
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['dislikes_count'];
    }

}