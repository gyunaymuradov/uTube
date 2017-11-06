<?php
namespace model\db;
use model\db\DBManager;
use model\FTPManager;
use model\Video;
use \PDO;
use \PDOException;

class VideoDao {
    private static $instance;
    private $pdo;
    private $ftpStream;

    const INSERT = "INSERT INTO videos 
                    (title, description, date_added, uploader_id, video_url, thumbnail_url, tag_id, hidden)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    const INSERT_TAGS = "INSERT INTO tags_videos (tag_id, video_id) VALUES (?, ?)";
    const DELETE = "UPDATE videos SET hidden=1 WHERE id = ?";
    const EDIT = "UPDATE videos SET title=?, description=?, tag_id = ? WHERE id=?";
    const EDIT_TAGS = "UPDATE tags_videos SET tag_id = ? WHERE video_id = ?";
    const GET_BY_ID ="SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url, tag_id, hidden 
                      FROM videos WHERE id=? AND hidden = 0";
    const GET_N_RANDOM = "SELECT v.id as video_id, v.title, v.video_url, v.thumbnail_url, v.hidden, v.tag_id, u.username as uploader_name, u.id as uploader_id 
                          FROM videos v JOIN users u ON v.uploader_id = u.id WHERE v.id != ? AND v.hidden = 0 LIMIT ?";
    const GET_N_RANDOM_BY_TAG_ID = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url 
                                    FROM videos WHERE id IN (SELECT video_id, hidden 
                                    FROM tags_videos WHERE tag_id = ? AND hidden = 0) 
                                    ORDER BY RAND() LIMIT ?";
    const GET_N_LATEST_BY_UPLOADER_ID = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url, tag_id, hidden 
                                          FROM videos WHERE uploader_id = ? AND hidden = 0 ORDER BY id DESC LIMIT ? OFFSET ?";
    const GET_NAME_SUGGESTIONS = "SELECT id, title, description, thumbnail_url, hidden FROM videos WHERE title LIKE ? AND hidden = 0";
    const GET_VIDEO_SUGGESTIONS = "SELECT id, title, description, video_url, thumbnail_url, hidden FROM videos WHERE title LIKE ? AND hidden = 0 LIMIT ? OFFSET ?";
    const GET_N_BY_NAME = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url, hidden 
                            FROM videos WHERE title LIKE ? AND hidden = 0 ORDER BY date_added DESC LIMIT ?";
    const IS_LIKED_OR_DISLIKED = "SELECT likes FROM video_likes_dislikes WHERE video_id = ? AND user_id = ?";
    const LIKE = "INSERT INTO video_likes_dislikes (video_id, user_id, likes) VALUES (?, ?, 1)";
    const UNLIKE = "DELETE FROM video_likes_dislikes WHERE video_id = ? AND user_id = ?";
    const UPDATE_LIKE_DISLIKE = "UPDATE video_likes_dislikes SET likes = ? WHERE video_id = ? AND user_id = ?";
    const DISLIKE = "INSERT INTO video_likes_dislikes (video_id, user_id, likes) VALUES (?, ?, 0)";
    const UNDISLIKE = "DELETE FROM video_likes_dislikes WHERE video_id = ? AND user_id = ?";
    const GET_LIKE_COUNT = "SELECT COUNT(*) as likes_count FROM video_likes_dislikes WHERE video_id = ? AND likes = 1";
    const GET_DISLIKE_COUNT = "SELECT COUNT(*) as dislikes_count FROM video_likes_dislikes WHERE video_id = ? AND likes = 0";
    const GET_TAGS = "SELECT tag_id FROM tags_videos WHERE video_id = ?";
    const GET_BY_PLAYLIST = "SELECT id, title, description, date_added, uploader_id, video_url, thumbnail_url, hidden, tag_id
                            FROM videos WHERE id IN (SELECT video_id FROM playlists_videos WHERE playlist_id = ?) AND hidden = 0";
    const GET_WITH_SAME_TAGS = "SELECT v.id as video_id, v.title, v.tag_id, v.video_url, v.thumbnail_url, v.hidden, u.username as uploader_name, u.id as uploader_id 
                                FROM videos v JOIN users u ON v.uploader_id = u.id 
                                WHERE tag_id = ? AND v.id != ? AND v.hidden = 0 LIMIT 10";
    const GET_TAG_OF_LAST_LIKED_VIDEO = "SELECT tag_id FROM tags_videos WHERE video_id = (
                                        SELECT video_id FROM video_likes_dislikes 
                                        WHERE user_id = ? AND likes = 1 ORDER BY id DESC LIMIT 1
                                        )";
    const GET_VIDEOS_OF_LAST_LIKED_TAG = "SELECT v.id as video_id, v.title, v.video_url, v.thumbnail_url, v.hidden 
                                          FROM videos v JOIN tags_videos t ON v.id = t.video_id 
                                          WHERE t.tag_id = ? AND v.hidden = 0";
    const GET_MOST_LIKED = "SELECT v.id, v.title, v.video_url, v.thumbnail_url, v.hidden, count(l.likes) AS likes_count, l. likes 
                            FROM videos v JOIN video_likes_dislikes l ON (v.id = l.video_id) 
                            GROUP BY (l.video_id) HAVING l.likes = 1 AND v.hidden = 0 ORDER BY likes_count DESC LIMIT ? OFFSET ?";
    const GET_RANDOM_TO_FILL_GAPS = "SELECT id, title, video_url, thumbnail_url, tag_id, hidden FROM videos WHERE hidden = 0 LIMIT ?";
    const GET_NEWEST = "SELECT id, title, video_url, thumbnail_url, hidden FROM videos WHERE hidden = 0 ORDER BY id DESC LIMIT ? OFFSET ?";
    const GET_VIDEOS_COUNT = "SELECT COUNT(*) as video_count FROM videos WHERE uploader_id = ? AND hidden = 0";
    const GET_TOTAL_COUNT_LIKED = "SELECT COUNT(DISTINCT vld.video_id) as total_liked_count FROM videos v 
                                  JOIN video_likes_dislikes vld ON vld.video_id = v.id WHERE vld.likes = 1";
    const GET_TOTAL_COUNT = "SELECT COUNT(*) as total_count FROM videos WHERE hidden = 0";
    const TITLE_EXISTS = "SELECT id, title, uploader_id FROM videos WHERE title = ? AND uploader_id = ?";

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
        $this->ftpStream = FTPManager::getInstance()->getStream();
    }
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new VideoDao();
        }
        return self::$instance;
    }

    public function getTotalLikedCount() {
        $statement = $this->pdo->prepare(self::GET_TOTAL_COUNT_LIKED);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getTotalCount() {
        $statement = $this->pdo->prepare(self::GET_TOTAL_COUNT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    /**
     * @param $uploaderId
     * @return array
     */
    public function getCountByUploaderId($uploaderId) {
        $statement = $this->pdo->prepare(self::GET_VIDEOS_COUNT);
        $statement->execute(array($uploaderId));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getNewest($limit, $offset) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_NEWEST);
        $statement->execute(array($limit, $offset));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (!file_exists($result[$key]['thumbnail_url'])) {
                ftp_get($this->ftpStream, $result[$key]['thumbnail_url'], "/htdocs/".$result[$key]['thumbnail_url'], FTP_BINARY);
            }
        }
        return $result;
    }

    public function getNRandomToFillGaps($limit) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_RANDOM_TO_FILL_GAPS);
        $statement->execute(array($limit));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMostLiked($limit, $offset) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_MOST_LIKED);
        $statement->execute(array($limit, $offset));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (!file_exists($result[$key]['video_url'])) {
                ftp_get($this->ftpStream, $result[$key]['video_url'], "/htdocs/".$result[$key]['video_url'], FTP_BINARY);
            }
            if (!file_exists($result[$key]['thumbnail_url'])) {
                ftp_get($this->ftpStream, $result[$key]['thumbnail_url'], "/htdocs/".$result[$key]['thumbnail_url'], FTP_BINARY);
            }
        }
        return $result;
    }

    public function getTagOfLastLiked($userId) {
        $statement = $this->pdo->prepare(self::GET_TAG_OF_LAST_LIKED_VIDEO);
        $statement->execute(array($userId));
        $tagId = $statement->fetchAll(PDO::FETCH_ASSOC)['tag_id'];
        return $tagId;
    }

    public function getVideosOfLastLikedTag($tagId) {
        $statement = $this->pdo->prepare(self::GET_VIDEOS_OF_LAST_LIKED_TAG);
        $statement->execute(array($tagId));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) < 6) {
            $difference = 6 - count($result);
        }
        return $result;
    }

    /**
     * Insert Video in DB
     * @param Video $video
     * @param array $tagIDs
     */
    public function insert(Video $video) {
            $statement = $this->pdo->prepare(self::INSERT);
            $statement->execute(array($video->getTitle(),
                $video->getDescription(),
                $video->getDateAdded(),
                $video->getUploaderID(),
                $video->getVideoURL(),
                $video->getThumbnailURL(),
                $video->getTagId(),
                $video->getHidden()
            ));
            ftp_put($this->ftpStream, "/htdocs/".$video->getVideoURL(), $video->getVideoURL(), FTP_BINARY);
            ftp_put($this->ftpStream, "/htdocs/".$video->getThumbnailURL(), $video->getThumbnailURL(), FTP_BINARY);
            return $this->pdo->lastInsertId();
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
                $videosArray[] = new Video(
                    $sqlResultSet[$key]['id'],
                    $sqlResultSet[$key]['title'],
                    $sqlResultSet[$key]['description'],
                    $sqlResultSet[$key]['date_added'],
                    $sqlResultSet[$key]['uploader_id'],
                    $sqlResultSet[$key]['video_url'],
                    $sqlResultSet[$key]['thumbnail_url'],
                    $sqlResultSet[$key]['tag_id'],
                    0
                );
                if (!file_exists($sqlResultSet[$key]['video_url'])) {
                    ftp_get($this->ftpStream, $sqlResultSet[$key]['video_url'], "/htdocs/".$sqlResultSet[$key]['video_url'], FTP_BINARY);
                }
                if (!file_exists($sqlResultSet[$key]['thumbnail_url'])) {
                    ftp_get($this->ftpStream, $sqlResultSet[$key]['thumbnail_url'], "/htdocs/".$sqlResultSet[$key]['thumbnail_url'], FTP_BINARY);
                }
            }
            return $videosArray;
        }
        elseif (isset($sqlResultSet['id'])) {
//            $tags = $this->getTags($sqlResultSet['id']);
            $video = new Video(
                $sqlResultSet['id'],
                $sqlResultSet['title'],
                $sqlResultSet['description'],
                $sqlResultSet['date_added'],
                $sqlResultSet['uploader_id'],
                $sqlResultSet['video_url'],
                $sqlResultSet['thumbnail_url'],
                $sqlResultSet['tag_id'],
                0
            );
            $video->setHidden($sqlResultSet['hidden']);
            if (!file_exists($sqlResultSet['video_url'])) {
                ftp_get($this->ftpStream, $sqlResultSet['video_url'], "/htdocs/".$sqlResultSet['video_url'], FTP_BINARY);
            }
            if (!file_exists($sqlResultSet['thumbnail_url'])) {
                ftp_get($this->ftpStream, $sqlResultSet['thumbnail_url'], "/htdocs/".$sqlResultSet['thumbnail_url'], FTP_BINARY);
            }
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
        $statement = $this->pdo->prepare(self::DELETE);
        $statement->execute(array($videoID));
    }
    /**
     * Edit video in DB
     * @param Video $video
     * @return int
     */
    public function edit(Video $video) {
        $statement = $this->pdo->prepare(self::EDIT);
        $statement->execute(array($video->getTitle(), $video->getDescription(), $video->getTagId(), $video->getId()));
        $rowsAffected = $statement->rowCount();
        ftp_delete($this->ftpStream, "/htdocs/".$video->getThumbnailURL());
        ftp_put($this->ftpStream, "/htdocs/".$video->getThumbnailURL(), $video->getThumbnailURL(), FTP_BINARY);
        return $rowsAffected;

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
        if (!$result) {
            return null;
        }
        return $this->sqlResultToVideoArray($result);
    }
    /**
     * Return array of N random Videos
     * @param int $numberOfVideos
     * @return array
     */
    public function getNRandom($numberOfVideos, $videoId){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_RANDOM);
        $statement->execute(array($numberOfVideos, $videoId));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (!file_exists($result[$key]['video_url'])) {
                ftp_get($this->ftpStream, $result[$key]['video_url'], "/htdocs/".$result[$key]['video_url'], FTP_BINARY);
            }
            if (!file_exists($result[$key]['thumbnail_url'])) {
                ftp_get($this->ftpStream, $result[$key]['thumbnail_url'], "/htdocs/".$result[$key]['thumbnail_url'], FTP_BINARY);
            }
        }
        return $result;
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
     * @param int $uploaderId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getNLatestByUploaderID($uploaderId, $limit = 4, $offset = 0){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare( self::GET_N_LATEST_BY_UPLOADER_ID);
        $statement->execute(array($uploaderId, $limit, $offset));
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
        $statement->execute(array("$partOfVideoName%"));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function searchByName($name, $limit, $offset) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_VIDEO_SUGGESTIONS);
        $statement->execute(array("%$name%", $limit, $offset));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (!file_exists($result[$key]['video_url'])) {
                ftp_get($this->ftpStream, $result[$key]['video_url'], "/htdocs/".$result[$key]['video_url'], FTP_BINARY);
            }
            if (!file_exists($result[$key]['thumbnail_url'])) {
                ftp_get($this->ftpStream, $result[$key]['thumbnail_url'], "/htdocs/".$result[$key]['thumbnail_url'], FTP_BINARY);
            }
        }
        return $result;
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

    public function getWithSameTags($tagId, $videoId) {
        $statement = $this->pdo->prepare(self::GET_WITH_SAME_TAGS);
        $statement->execute(array($tagId[0], $videoId));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $key => $value) {
            if (!file_exists($result[$key]['video_url'])) {
                ftp_get($this->ftpStream, $result[$key]['video_url'], "/htdocs/".$result[$key]['video_url'], FTP_BINARY);
            }
            if (!file_exists($result[$key]['thumbnail_url'])) {
                ftp_get($this->ftpStream, $result[$key]['thumbnail_url'], "/htdocs/".$result[$key]['thumbnail_url'], FTP_BINARY);
            }
        }
        if (count($result) < 10) {
            $limit = 10 - count($result);
            $moreVideos = $this->getNRandom($videoId, $limit);
            foreach ($moreVideos as $video) {
                $result[] = $video;
            }
        }
        return $result;
    }

    public function checkTitleExists($title, $creatorId) {
        $statement = $this->pdo->prepare(self::TITLE_EXISTS);
        $statement->execute(array($title, $creatorId));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            return true;
        }
        else {
            return false;
        }
    }
}