<?php
namespace model\db;
use model\db\DBManager;
use model\Video;
use \PDO;
use \PDOException;

class VideoDao {
    private static $instance;
    private $pdo;
    const INSERT_VIDEO = "INSERT INTO videos (title, description, date_added, uploader_id, video_url, thumbnail_url, hidden) VALUES (?, ?, ?, ?, ?, ?, ?)";
    const INSERT_TAGS = "INSERT INTO tags_videos (tag_id, video_id) VALUES (?, ?)";
    const DELETE_VIDEO = "UPDATE TABLE videos SET hidden=1 WHERE id = ?";
    const EDIT_VIDEO = "UPDATE TABLE videos SET title=?, description=? WHERE id=?";
    const GET_BY_ID ="SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url, hidden FROM videos WHERE id=?";
    const GET_N_RANDOM = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url FROM videos WHERE hidden=0 ORDER BY RAND() LIMIT ?";
    const GET_N_RANDOM_BY_TAG_ID = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url 
                                    FROM videos WHERE id IN (SELECT video_id FROM tags_videos WHERE tag_id = ?) ORDER BY RAND() LIMIT ?";
    const GET_N_LATEST_BY_UPLOADER_ID = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url 
                                          FROM videos WHERE uploader_id=? ORDER BY date_added DESC LIMIT ?";
    const GET_NAME_SUGGESTIONS = "SELECT title FROM videos WHERE title LIKE ? LIMIT 5";
    const GET_N_BY_NAME = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url 
                            FROM videos WHERE title LIKE ? ORDER BY date_added DESC LIMIT ?";
    const IS_LIKED_OR_DISLIKED = "SELECT likes FROM video_likes_dislikes WHERE video_id = ? AND user_id = ?";
    const LIKE = "INSERT INTO video_likes_dislikes (video_id, user_id, likes) VALUES (?, ?, 1)";
    const UNLIKE = "DELETE FROM video_likes_dislikes WHERE video_id = ? AND user_id = ?";
    const UPDATE_LIKE_DISLIKE = "UPDATE video_likes_dislikes SET likes = ? WHERE video_id = ? AND user_id = ?";
    const DISLIKE = "INSERT INTO video_likes_dislikes (video_id, user_id, likes) VALUES (?, ?, 0)";
    const UNDISLIKE = "DELETE FROM video_likes_dislikes WHERE video_id = ? AND user_id = ?";
    const GET_LIKE_COUNT = "SELECT COUNT(*) as likes_count FROM video_likes_dislikes WHERE video_id = ? AND likes = 1";
    const GET_DISLIKE_COUNT = "SELECT COUNT(*) as dislikes_count FROM video_likes_dislikes WHERE video_id = ? AND likes = 0";
    const GET_TAGS = "SELECT tag_id FROM tags_videos WHERE video_id = ?";
    const GET_BY_PLAYLIST = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url
                            FROM videos WHERE id IN (SELECT video_id FROM playlists_videos WHERE playlist_id = ?) AND hidden = 0";

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
    public function insert(Video $video) {
        try {
            $this->pdo->beginTransaction();
            $statement = $this->pdo->prepare(self::INSERT_VIDEO);
            $statement->execute(array($video->getTitle(),
                $video->getDescription(),
                $video->getDateAdded(),
                $video->getUploaderID(),
                $video->getVideoURL(),
                $video->getThumbnailURL(),
                $video->getHidden()
            ));
            $video->setId($this->pdo->lastInsertId());
            foreach ($video->getTags() as $tagID) {
                $statement = $this->pdo->prepare(self::INSERT_TAGS);
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
     * Converts PDO SQL Result Set to an Array of Video Objects
     * or one Video Object depending on elements in sqlResultSet
     * @param array $sqlResultSet
     * @return array|Video
     */
    private function sqlResultToVideoArray(Array $sqlResultSet) {
        if(isset($sqlResultSet[0])) {
            $videosArray = array();
            foreach ($sqlResultSet as $key => $value) {
                $tags = $this->getTags($sqlResultSet[$key]['id']);
                $videosArray[] = new Video(
                    $sqlResultSet[$key]['id'],
                    $sqlResultSet[$key]['title'],
                    $sqlResultSet[$key]['description'],
                    $sqlResultSet[$key]['date_added'],
                    $sqlResultSet[$key]['uploader_id'],
                    $sqlResultSet[$key]['video_url'],
                    $sqlResultSet[$key]['thumbnail_url'],
                    $tags
                );
            }
            return $videosArray;
        }
        elseif (isset($sqlResultSet['id'])) {
            $tags = $this->getTags($sqlResultSet['id']);
            $video = new Video(
                $sqlResultSet['id'],
                $sqlResultSet['title'],
                $sqlResultSet['description'],
                $sqlResultSet['date_added'],
                $sqlResultSet['uploader_id'],
                $sqlResultSet['video_url'],
                $sqlResultSet['thumbnail_url'],
                $tags
            );
            $video->setHidden($sqlResultSet['hidden']);
            return $video;
        }
        else {
            return array();
        }
    }
    /**
     * Delete video from DB by Video id
     * @param int $videoID
     */
    public function delete($videoID) {
        $statement = $this->pdo->prepare(self::DELETE_VIDEO);
        $statement->execute(array($videoID));
    }
    /**
     * Edit video in DB
     * @param Video $video
     */
    public function edit(Video $video) {
        $statement = $this->pdo->prepare(self::EDIT_VIDEO);
        $statement->execute(array($video->getTitle(), $video->getDescription(), $video->getId()));
    }
    /**
     * Return one Video by Video id
     * @param int $videoID
     * @return Video
     */
    public function getByID($videoID) {
        $statement = $this->pdo->prepare(self::GET_BY_ID);
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $this->sqlResultToVideoArray($result);
    }
    /**
     * Return array of N random Videos
     * @param int $numberOfVideos
     * @return array
     */
    public function getNRandom($numberOfVideos){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_RANDOM);
        $statement->execute(array($numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToVideoArray($result);
    }
    /**
     * Return array of N random Videos by Tag id
     * @param int $numberOfVideos
     * @param int $tagID
     * @return array
     */
    public function getNRandomByTagID($numberOfVideos, $tagID){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_RANDOM_BY_TAG_ID);
        $statement->execute(array($tagID, $numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToVideoArray($result);
    }
    /**
     * Return array of N Videos by Uploader id
     * @param int $numberOfVideos
     * @param int $uploaderID
     * @return array
     */
    public function getNLatestByUploaderID($numberOfVideos, $uploaderID){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare( self::GET_N_LATEST_BY_UPLOADER_ID);
        $statement->execute(array($uploaderID, $numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToVideoArray($result);
    }
    /**
     * Return array of 5 Videos by title
     * For search box suggestions
     * @param string $partOfVideoName
     * @return array
     */
    public function getNameSuggestions($partOfVideoName) {
        $statement = $this->pdo->prepare(self::GET_NAME_SUGGESTIONS);
        $statement->execute(array("%$partOfVideoName%"));
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
    public function getNByName($videoName, $numberOfVideos) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_BY_NAME);
        $statement->execute(array("%$videoName%", $numberOfVideos));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToVideoArray($result);
    }
    /**
     * Return true if Video is liked by User
     * Return false if Video is disliked by User
     * Return null if Video is not liked or disliked by User
     * @param int $videoID
     * @param int $userID
     * @return bool|null
     */
    public function isLikedOrDislikedByUser($videoID, $userID) {
        $statement = $this->pdo->prepare(self::IS_LIKED_OR_DISLIKED);
        $statement->execute(array($videoID, $userID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (isset($result['likes'])) {
            if ($result['likes'] == '1') {
                return true;
            }
            elseif ($result['likes'] == '0') {
                return false;
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
    public function like($videoID, $userID) {
        $likes = $this->isLikedOrDislikedByUser($videoID, $userID);
        //if video is not liked on pressing button 'like' like is added
        if (is_null($likes)) {
            $statement = $this->pdo->prepare(self::LIKE);
            $statement->execute(array($videoID, $userID));
        }
        elseif ($likes == true) {
            //if already liked on pressing button 'like' again the like is removed
            $statement = $this->pdo->prepare(self::UNLIKE);
            $statement->execute(array($videoID, $userID));
        } else {
            $statement = $this->pdo->prepare(self::UPDATE_LIKE_DISLIKE);
            $statement->execute(array('1', $videoID, $userID));
        }
    }
    /**
     * User with userID Dislikes video with videoID
     * @param int $videoID
     * @param int $userID
     */
    public function dislike($videoID, $userID) {
        $likes = $this->isLikedOrDislikedByUser($videoID, $userID);
        //if comment is not disliked on pressing button 'dislike' dislike is added
        if (is_null($likes)) {
            $statement = $this->pdo->prepare(self::DISLIKE);
            $statement->execute(array($videoID, $userID));
        }
        elseif ($likes == false) {
            //if already disliked on pressing button 'dislike' again the dislike is removed
            $statement = $this->pdo->prepare(self::UNDISLIKE);
            $statement->execute(array($videoID, $userID));
        } else {
            $statement = $this->pdo->prepare(self::UPDATE_LIKE_DISLIKE);
            $statement->execute(array('0', $videoID, $userID));
        }
    }
    /**
     * Get number of likes on Video by Video id
     * @param $videoID
     * @return mixed
     */
    public function getLikesCountById($videoID) {
        $statement = $this->pdo->prepare(self::GET_LIKE_COUNT);
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['likes_count'];
    }
    /**
     * Get number of dislikes on Video by Video id
     * @param $videoID
     * @return mixed
     */
    public function getDislikesCountById($videoID) {
        $statement = $this->pdo->prepare(self::GET_DISLIKE_COUNT);
        $statement->execute(array($videoID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['dislikes_count'];
    }
    /**
     * Return array of all tags for Video
     * @param int $videoID
     * @return array
     */
    public function getTags($videoID) {
        $statement = $this->pdo->prepare(self::GET_TAGS);
        $statement->execute(array($videoID));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $tags = array();
        foreach ($result as $key=>$value) {
            $tags[] = $result[$key]['tag_id'];
        }
        return $tags;
    }

    public function getByPlaylist($playlistID) {
        $statement = $this->pdo->prepare(self::GET_BY_PLAYLIST);
        $statement->execute(array($playlistID));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToVideoArray($result);
    }
}