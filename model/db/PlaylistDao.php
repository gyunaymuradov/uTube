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
    const INSERT_PLAYLIST = "INSERT INTO playlists (title, date_added, creator_id, thumbnail_url) VALUES (?, ?, ?)";
    const INSERT_VIDEO = "INSERT INTO playlists_videos (playlist_id, video_id) VALUES (?, ?)";
    const UPDATE_TITLE = "UPDATE TABLE playlists SET title=? WHERE id=?";
    const DELETE_VIDEO = "DELETE FROM playlists_videos WHERE playlist_id = ? AND video_id = ?";
    const GET_BY_ID = "SELECT title, date_added, creator_id thumbnail_url FROM playlists WHERE id=?";
    const GET_N_LATEST_BY_CREATOR = "SELECT id, title, date_added, creator_id, thumbnail_url FROM playlists WHERE creator_id=? ORDER BY date_added DESC LIMIT ?";
    const GET_N_BY_VIDEO_ID = "SELECT id, title, date_added, creator_id, thumbnail_url 
                            FROM playlists WHERE id IN (SELECT playlist_id FROM playlists_videos WHERE video_id=?) LIMIT ?";
    const GET_N_BY_VIDEO_NAME = "SELECT id, title, date_added, creator_id, thumbnail_url
                                  FROM playlists WHERE id IN (SELECT playlist_id FROM playlists_videos 
                                  WHERE video_id IN (SELECT id FROM videos WHERE title LIKE '%?%')) LIMIT ?";
    const GET_NAME_SUGGESTIONS = "SELECT title FROM playlists WHERE title LIKE '%?%' LIMIT 5";
    const GET_N_BY_NAME = "SELECT id, title, date_added, creator_id, thumbnail_url FROM playlists WHERE title LIKE '%?%' ORDER BY date_added DESC LIMIT ?";
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
    /** Converts PDO SQL Result Set to an Array of Playlist Objects
     * or one Playlist Object depending on elements in sqlResultSet
     * @param array $sqlResultSet
     * @return array|Playlist
     */
    private function sqlResultToPlaylistArray(Array $sqlResultSet) {
        if(isset($sqlResultSet[0])) {
            $playlistsArray = array();
            foreach ($sqlResultSet as $key=>$value) {
                $videosArray = $this->videoDao->getByPlaylist($sqlResultSet[$key]['id']);
                $playlistsArray[] = new Playlist(
                    $sqlResultSet[$key]['id'],
                    $sqlResultSet[$key]['title'],
                    $sqlResultSet[$key]['date_added'],
                    $sqlResultSet[$key]['creator_id'],
                    $sqlResultSet[$key]['thumbnail_id'],
                    $videosArray
                );
            }
            return $playlistsArray;
        }
        else {
            $videosArray = $this->videoDao->getByPlaylist($sqlResultSet['id']);
            $playlist = new Playlist(
                $sqlResultSet['id'],
                $sqlResultSet['title'],
                $sqlResultSet['date_added'],
                $sqlResultSet['creator_id'],
                $sqlResultSet['thumbnail_id'],
                $videosArray
            );
            return $playlist;
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
    /**
     * Changes title of existing Playlist
     * @param Playlist $playlist
     * Playlist id is of the renamed playlist
     * Playlist title is the new title
     */
    public function changeTitle(Playlist $playlist) {
        $statement = $this->pdo->prepare(self::UPDATE_TITLE);
        $statement->execute(array($playlist->getTitle(), $playlist->getId()));
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
     * @param int $playlistID
     * @param int $videoID
     */
    public function deleteVideo($playlistID, $videoID) {
        $statement = $this->pdo->prepare(self::DELETE_VIDEO);
        $statement->execute(array($playlistID, $videoID));
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
     * @param int $numberOfPlaylists
     * @param int $creatorID
     * @return array
     */
    public function getNLatestByCreatorID($numberOfPlaylists, $creatorID){
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_LATEST_BY_CREATOR);
        $statement->execute(array($creatorID, $numberOfPlaylists));
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
        $statement->execute(array($videoName, $numberOfPlaylists));
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
        $statement->execute(array($partOfPlaylistName));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $playlistsNamesArray = array();
        foreach ($result as $key=>$value) {
            $playlistsNamesArray[] = $result[$key]['title'];
        }
        return $playlistsNamesArray;
    }
    /**
     * Return array of N Playlists by Playlist name
     * @param string $playlistName
     * @param int $numberOfPlaylists
     * @return array
     */
    public function getNByName($playlistName, $numberOfPlaylists) {
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $statement = $this->pdo->prepare(self::GET_N_BY_NAME);
        $statement->execute(array($playlistName, $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $this->sqlResultToPlaylistArray($result);
    }
}