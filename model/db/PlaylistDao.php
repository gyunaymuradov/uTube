<?php

namespace model\db;

use model\db\DBManager;
use model\Playlist;
use \PDO;
use \PDOException;

class PlaylistDao {
    private static $instance;
    private $pdo;

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new PlaylistDao();
        }
        return self::$instance;
    }

    /**
     * Inserts a new Playlist in DB
     * @param Playlist $playlist
     */
    public function insertPlaylist(Playlist $playlist) {
        try {
            $this->pdo->beginTransaction();
            $statement = $this->pdo->prepare("INSERT INTO playlists (title, date_added, creator_id) VALUES (?, ?, ?)");
            $statement->execute(array($playlist->getTitle(), $playlist->getDateAdded(), $playlist->getCreatorID()));
            $playlist->setId($this->pdo->lastInsertId());
            foreach ($playlist->getVideosIDs() as $videoID) {
                $statement = $this->pdo->prepare("INSERT INTO playlists_videos (video_id, playlist_id) VALUES (?, ?)");
                $statement->execute(array($videoID, $playlist->getId()));
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
    public function changePlaylistTitle(Playlist $playlist) {
        $statement = $this->pdo->prepare("UPDATE TABLE playlists SET title=? WHERE id=?");
        $statement->execute(array($playlist->getTitle(), $playlist->getId()));
    }

    /**
     * Inserts video in playlist by IDs
     * @param int $playlistID
     * @param int $videoID
     */
    public function insertVideoInPlaylist($playlistID, $videoID) {
        $statement = $this->pdo->prepare("INSERT INTO playlists_videos (playlist_id, video_id) VALUES (?, ?)");
        $statement->execute(array($playlistID, $videoID));
    }

    /**
     * Removes video from playlist by IDs
     * @param int $playlistID
     * @param int $videoID
     */
    public function deleteVideoFromPlaylist($playlistID, $videoID) {
        $statement = $this->pdo->prepare("DELETE FROM playlists_videos WHERE playlist_id = ? AND video_id = ?");
        $statement->execute(array($playlistID, $videoID));
    }

    /**
     * Return playlist from DB by ID
     * @param int $playlistID
     * @return Playlist
     */
    public function getOnePlaylist($playlistID) {
        //get playlist from playlists table
        $statement = $this->pdo->prepare("SELECT title, date_added, creator_id FROM playlists WHERE id=?");
        $statement->execute(array($playlistID));
        $resultPlaylist = $statement->fetch(PDO::FETCH_ASSOC);
        //get videos in playlist from playlists_videos table
        $statement = $this->pdo->prepare("SELECT video_id FROM playlists_videos WHERE playlist_id=?");
        $statement->execute(array($playlistID));
        $resultVideos = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosIDs = array();
        foreach ($resultVideos as $key=>$value) {
            $videosIDs[] = $resultVideos[$key]['video_id'];
        }
        $playlist = new Playlist($playlistID, $resultPlaylist['title'], $resultPlaylist['date_added'], $resultPlaylist['creator_id'], $videosIDs);
        return $playlist;
    }

    /**
     * Return an array of N Playlists from DB by Creator id
     * @param int $numberOfPlaylists
     * @param int $creatorID
     * @return array
     */
    public function getNLatestPlaylistsByCreatorID($numberOfPlaylists, $creatorID){
        $statement = $this->pdo->prepare("SELECT id, title, date_added, creator_id FROM playlists WHERE creator_id=? ORDER BY date_added DESC LIMIT ?");
        $statement->execute(array($creatorID, $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $playlistsArray = array();
        foreach ($result as $key=>$value) {
            $playlistsArray[] = new Playlist($result[$key]['id'], $result[$key]['title'], $result[$key]['date_added'], $result[$key]['creator_id'], []);
        }
        return $playlistsArray;
    }

    /**
     * Return array of N Playlists by Video id
     * @param int $numberOfPlaylists
     * @param int $videoID
     * @return array
     */
    public function getNPlaylistsByVideoID($numberOfPlaylists, $videoID) {
        $statement = $this->pdo->prepare("SELECT id, title, date_added, creator_id FROM playlists WHERE id IN (SELECT playlist_id FROM playlists_videos WHERE video_id=?) LIMIT ?");
        $statement->execute(array($videoID, $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $playlistsArray = array();
        foreach ($result as $key=>$value) {
            $playlistsArray[] = new Playlist($result[$key]['id'], $result[$key]['title'], $result[$key]['date_added'], $result[$key]['creator_id'], []);
        }
        return $playlistsArray;
    }

    /**
     * Return array of N Playlists by Video name
     * @param int $numberOfPlaylists
     * @param int $videoName
     * @return array
     */
    public function getNPlaylistsByVideoName($numberOfPlaylists, $videoName) {
        $statement = $this->pdo->prepare("SELECT id, title, date_added, creator_id FROM playlists WHERE id IN (SELECT playlist_id FROM playlists_videos WHERE video_id IN (SELECT id FROM videos WHERE title LIKE '%?%')) LIMIT ?");
        $statement->execute(array($videoName, $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $playlistsArray = array();
        foreach ($result as $key=>$value) {
            $playlistsArray[] = new Playlist($result[$key]['id'], $result[$key]['title'], $result[$key]['date_added'], $result[$key]['creator_id'], []);
        }
        return $playlistsArray;
    }

    /**
     * Return array of 5 Playlists by title
     * For search box suggestions
     * @param string $partOfPlaylistName
     * @return array
     */
    public function getPlaylistNameSuggestionsForSearch($partOfPlaylistName) {
        $statement = $this->pdo->prepare("SELECT title FROM playlists WHERE title LIKE '%?%' LIMIT 5");
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
    public function getNPlaylistsByName($playlistName, $numberOfPlaylists) {
        $statement = $this->pdo->prepare("SELECT id, title, date_added, creator_id FROM playlists WHERE title LIKE '%?%' ORDER BY date_added DESC LIMIT ?");
        $statement->execute(array($playlistName, $numberOfPlaylists));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $videosArray = array();
        foreach ($result as $key=>$value) {
            $videosArray[] = new Playlist($result[$key]['id'], $result[$key]['title'], $result[$key]['date_added'], $result[$key]['creator_id'], []);
        }
        return $videosArray;
    }
}