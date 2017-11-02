<?php
namespace model\db;
use model\db\DBManager;
use model\Playlist;
use \PDO;
use \PDOException;
use model\Video;
class PlaylistDao {
    private static $instance;
    private $pdo;
    private $videoDao;
    const INSERT_PLAYLIST = "INSERT INTO playlists (title, date_added, creator_id, thumbnail_url) VALUES (?, ?, ?, ?)";
    const INSERT_VIDEO = "INSERT INTO playlists_videos (playlist_id, video_id) VALUES (?, ?)";
    const UPDATE_TITLE = "UPDATE playlists SET title=? WHERE id=?";
    const UPDATE_THUMBNAIL = "UPDATE playlists SET thumbnail_url=? WHERE id=?";
    const DELETE_VIDEO = "DELETE FROM playlists_videos WHERE playlist_id = ? AND video_id = ?";
    const DELETE_PLAYLIST = "DELETE FROM playlists WHERE id = ?";
    const DELETE_ALL_VIDEOS = "DELETE FROM playlists_videos WHERE playlist_id = ?";
    const GET_BY_ID = "SELECT id, title, date_added, creator_id, thumbnail_url FROM playlists WHERE id = ?";
    const GET_N_LATEST_BY_CREATOR = "SELECT id, title, date_added, creator_id, thumbnail_url 
                                    FROM playlists WHERE creator_id=? ORDER BY id DESC LIMIT ? OFFSET ?";
    const GET_N_BY_VIDEO_ID = "SELECT id, title, date_added, creator_id, thumbnail_url 
                            FROM playlists WHERE id IN (SELECT playlist_id FROM playlists_videos WHERE video_id=?) LIMIT ?";
    const GET_N_BY_VIDEO_NAME = "SELECT id, title, date_added, creator_id, thumbnail_url
                                  FROM playlists WHERE id IN (SELECT playlist_id FROM playlists_videos 
                                  WHERE video_id IN (SELECT id FROM videos WHERE title LIKE ?)) LIMIT ?";
    const GET_NAME_SUGGESTIONS = "SELECT id, title FROM playlists WHERE title LIKE ?";
    const SEARCH_BY_NAME = "SELECT p.id as playlist_id, p.title, p.date_added, p.creator_id, p.thumbnail_url, u.username, u.user_photo_url, count(pv.video_id) as video_count 
                            FROM playlists p JOIN users u ON p.creator_id = u.id 
                            JOIN playlists_videos pv ON p.id = pv.playlist_id 
                            WHERE p.title LIKE ? GROUP BY pv.playlist_id";
    const GET_VIDEOS_BY_ID = "SELECT v.id, v.title, v.uploader_id, v.thumbnail_url, u.username 
                              FROM videos v JOIN users u ON u.id = v.uploader_id 
                              JOIN playlists_videos pv ON pv.video_id = v.id 
                              JOIN playlists p ON p.id = pv.playlist_id WHERE p.id = ? AND v.hidden = 0";
    const GET_PLAYLISTS_COUNT = "SELECT COUNT(*) as playlist_count FROM playlists WHERE creator_id = ?";

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
        $this->videoDao = VideoDao::getInstance();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new PlaylistDao();
        }
        return self::$instance;
    }

    /**
     * @param int $creatorId
     * @return array
     */
    public function getCountByCreatorId($creatorId) {
        $statement = $this->pdo->prepare(self::GET_PLAYLISTS_COUNT);
        $statement->execute(array($creatorId));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /** Converts PDO SQL Result Set to an Array of Playlist Objects
     * or one Playlist Object depending on elements in sqlResultSet
     * @param array $sqlResultSet
     * @return array|Playlist
     */
    private function sqlResultToPlaylistArray($sqlResultSet) {
        if(isset($sqlResultSet[0])) {
            $playlistsArray = array();
            foreach ($sqlResultSet as $key=>$value) {
                $videosArray = $this->videoDao->getByPlaylist($sqlResultSet[$key]['id']);
                $playlistsArray[] = new Playlist(
                    $sqlResultSet[$key]['id'],
                    $sqlResultSet[$key]['title'],
                    $sqlResultSet[$key]['date_added'],
                    $sqlResultSet[$key]['creator_id'],
                    $sqlResultSet[$key]['thumbnail_url'],
                    $videosArray
                );
            }
            return $playlistsArray;
        }
        elseif (isset($sqlResultSet['id'])) {
            $videosArray = $this->videoDao->getByPlaylist($sqlResultSet['id']);
            $playlist = new Playlist(
                $sqlResultSet['id'],
                $sqlResultSet['title'],
                $sqlResultSet['date_added'],
                $sqlResultSet['creator_id'],
                $sqlResultSet['thumbnail_url'],
                $videosArray
            );
            return $playlist;
        }
        else {
            return null;
        }
    }

    /**
     * Inserts a new Playlist in DB
     * @param Playlist $playlist
     */
    public function insert(Playlist $playlist) {
        try {
            $this->pdo->beginTransaction();
            $statement = $this->pdo->prepare(self::INSERT_PLAYLIST);
            $statement->execute(array($playlist->getTitle(), $playlist->getDateAdded(), $playlist->getCreatorID(), $playlist->getThumbnailURL()));
            $playlist->setId($this->pdo->lastInsertId());
            foreach ($playlist->getVideosIDs() as $videoID) {
                $statement = $this->pdo->prepare(self::INSERT_VIDEO);
                $statement->execute(array($playlist->getId(), $videoID));
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

    public function delete($playlistId) {
        try {
            $this->pdo->beginTransaction();
            $statement = $this->pdo->prepare(self::DELETE_ALL_VIDEOS);
            $statement->execute(array($playlistId));
            $statement = $this->pdo->prepare(self::DELETE_PLAYLIST);
            $statement->execute(array($playlistId));
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
     * Changes title of existing Playlist
     * @param $playlistId
     * @param $newTitle
     */
    public function changeTitle($playlistId, $newTitle) {
        $statement = $this->pdo->prepare(self::UPDATE_TITLE);
        $statement->execute(array($newTitle, $playlistId));
    }
    public function changeThumbnail($playlistId, $newThumbnailURL) {
        $statement = $this->pdo->prepare(self::UPDATE_THUMBNAIL);
        $statement->execute(array($newThumbnailURL, $playlistId));
    }
    /**
     * Inserts video in playlist by IDs
     * @param int $playlistID
     * @param int $videoID
     */
    public function insertVideo($playlistID, $videoID) {
        $statement = $this->pdo->prepare(self::INSERT_VIDEO);
        $statement->execute(array($playlistID, $videoID));
    }

    /**
     * Removes video from playlist by IDs
     * Returns true if whole playlist is deleted and false if it is not
     * @param $playlistID
     * @param $videoID
     * @return bool
     */
    public function deleteVideo($playlistID, $videoID) {
        $statement = $this->pdo->prepare(self::DELETE_VIDEO);
        $statement->execute(array($playlistID, $videoID));

        $statement = $this->pdo->prepare(self::GET_VIDEOS_BY_ID);
        $statement->execute(array($playlistID));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        if (empty($result)) {
            $statement = $this->pdo->prepare(self::DELETE_PLAYLIST);
            $statement->execute(array($playlistID));
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Return playlist from DB by ID
     * @param int $playlistID
     * @return Playlist
     */
    public function getByID($playlistID) {
        //get playlist from playlists table
        $statement = $this->pdo->prepare(self::GET_BY_ID);
        $statement->execute(array($playlistID));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $this->sqlResultToPlaylistArray($result);
    }

    /**
     * Return an array of N Playlists from DB by Creator id
     * @param int $creatorId
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getNLatestByCreatorID($creatorId, $limit = 4, $offset = 0){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_LATEST_BY_CREATOR);
        $statement->execute(array($creatorId, $limit, $offset));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToPlaylistArray($result);
    }

    /**
     * Return array of N Playlists by Video id
     * @param int $numberOfPlaylists
     * @param int $videoID
     * @return array
     */
    public function getNByVideoID($numberOfPlaylists, $videoID) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_BY_VIDEO_ID);
        $statement->execute(array($videoID, $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToPlaylistArray($result);
    }

    /**
     * Return array of N Playlists by Video name
     * @param int $numberOfPlaylists
     * @param int $videoName
     * @return array
     */
    public function getNByVideoName($numberOfPlaylists, $videoName) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_BY_VIDEO_NAME);
        $statement->execute(array("%$videoName%", $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToPlaylistArray($result);
    }

    /**
     * Return array of 5 Playlists by title
     * For search box suggestions
     * @param string $partOfPlaylistName
     * @return array
     */
    public function getNameSuggestions($partOfPlaylistName) {
        $statement = $this->pdo->prepare(self::GET_NAME_SUGGESTIONS);
        $statement->execute(array("$partOfPlaylistName%"));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Return array of N Playlists by Playlist name
     * @param string $playlistName
     * @param int $numberOfPlaylists
     * @return array
     */
    public function searchByName($playlistName) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::SEARCH_BY_NAME);
        $statement->execute(array("%$playlistName%"));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getVideoById($playlistId) {
        $statement = $this->pdo->prepare(self::GET_VIDEOS_BY_ID);
        $statement->execute(array($playlistId));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}