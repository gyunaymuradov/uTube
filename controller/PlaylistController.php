<?php

namespace controller;

use model\Comment;
use model\db\CommentDao;
use model\db\PlaylistDao;
use model\db\TagDao;
use model\db\UserDao;
use model\User;
use model\db\VideoDao;
use model\Video;
use model\Playlist;

class PlaylistController extends BaseController
{

    public function __construct()
    {
    }

    public function getNames() {
        try {
            $playlistDao = PlaylistDao::getInstance();
            $result = $playlistDao->getNLatestByCreatorID(100, $_SESSION['user']->getId());
        }
        catch (\Exception $e) {
            $result = array("Result" => "Error");
        }
        $this->jsonEncodeParams($result);
    }

    public function createPlaylist() {
        try {
            if (isset($_GET['title']) && $_GET['title'] != "" && isset($_GET['videoID']) && $_GET['videoID'] != "" && isset($_SESSION['user'])) {
                $playlistDao = PlaylistDao::getInstance();
                $videoDao = VideoDao::getInstance();
                $title = htmlspecialchars($_GET['title']);
                $date = date("Y-m-d");
                $userID = $_SESSION['user']->getId();
                $videoID = htmlspecialchars($_GET['videoID']);
                $video = $videoDao->getByID($videoID);
                $thumbnailURL = $video->getThumbnailURL();
                $playlist = new Playlist(null, $title, $date, $userID, $thumbnailURL, array($videoID));
                $playlistDao->insert($playlist);
                $result = $playlistDao->getNLatestByCreatorID(10, $userID);
//                $result = array("Result" => "Playlist created successfully. The video has been added in it.");
            }
            else {
                $result = array("Result" => "Error! You cant leave empty fields!");
            }
        }
        catch (\Exception $e) {
            $result = array("Result" => "Error! Please try again later!");
        }
        $this->jsonEncodeParams($result);
    }

    public function insertVideo() {
        try {
            if (isset($_GET['playlistID']) && $_GET['playlistID'] != "" && isset($_GET['videoID']) && $_GET['videoID'] != "" && isset($_SESSION['user'])) {
                $playlistDao = PlaylistDao::getInstance();
                $videoDao = VideoDao::getInstance();
                $playlistID = htmlspecialchars($_GET['playlistID']);
                $videoID = htmlspecialchars($_GET['videoID']);
                $playlistFromDb = $playlistDao->getByID($playlistID);
                $videoFromDb = $videoDao->getByID($videoID);
                $userID = $_SESSION['user']->getId();
                if (!is_null($playlistFromDb) && $_SESSION['user']->getId() == $playlistFromDb->getCreatorID() && !is_null($videoFromDb)) {
                    $playlistDao->insertVideo($playlistID, $videoID);
                    $playlistDao->changeThumbnail($playlistID, $videoFromDb->getThumbnailURL());
                    $result = $playlistDao->getNLatestByCreatorID(10, $userID);
//                    $result = array("Result" => "Video successfully added!");
                }
                else {
                    $result = array("Result" => "Invalid action!");
                }
            }
            else {
                $result = array("Result" => "Error! You cant leave empty fields!");
            }
        }
        catch (\Exception $e) {
            $result = array("Result" => "Error! Please try again later!");
        }
        $this->jsonEncodeParams($result);
    }

    public function renamePlaylist() {
        try {
            if (isset($_GET['playlistID']) && $_GET['playlistID'] != "" && isset($_GET['newTitle']) && $_GET['newTitle'] != "" && isset($_SESSION['user'])) {
                $playlistDao = PlaylistDao::getInstance();
                $playlistID = htmlspecialchars($_GET['playlistID']);
                $newTitle = htmlspecialchars($_GET['newTitle']);
                $playlistFromDb = $playlistDao->getByID($playlistID);
                if (!is_null($playlistFromDb) && $_SESSION['user']->getId() == $playlistFromDb->getCreatorID()) {
                    $playlistDao->changeTitle($playlistID, $newTitle);
                    $result = array("Result" => "Playlist successfully renamed!");
                }
                else {
                    $result = array("Result" => "Invalid action!");
                }
            }
            else {
                $result = array("Result" => "Error! You cant leave empty fields!");
            }
        }
        catch (\Exception $e) {
            $result = array("Result" => "Error! Please try again later!");
        }
        $this->jsonEncodeParams($result);
    }

    public function getVideos() {
        if (isset($_GET['playlistID']) && $_GET['playlistID'] != "") {
            try {
                $playlistDao = PlaylistDao::getInstance();
                $result = $playlistDao->getVideoById($_GET['playlistID']);
            } catch (\Exception $e) {
                $result = array("Result" => "Error");
            }
        }
        else {
            $result = array("Result" => "Error");
        }
        $this->jsonEncodeParams($result);
    }

    public function removeVideo() {
        if (isset($_GET['playlistID']) && $_GET['playlistID'] != "" && isset($_GET['videoID']) && $_GET['videoID'] != "" && isset($_SESSION['user'])) {
            try {
                $playlistDao = PlaylistDao::getInstance();
                $playlistID = htmlspecialchars($_GET['playlistID']);
                $videoID = htmlspecialchars($_GET['videoID']);
                $playlistFromDb = $playlistDao->getByID($playlistID);
                $userID = $_SESSION['user']->getId();
                if(!is_null($playlistFromDb) && $userID == $playlistFromDb->getCreatorID()) {
                    $isPlaylistDeleted = $playlistDao->deleteVideo($playlistID, $videoID);
                    if ($isPlaylistDeleted) {
                        $result = array("Result" => "Playlist deleted!");
                    } else {
                        $videosInPlaylist = $playlistDao->getVideoById($playlistID);
                        $playlistDao->changeThumbnail($playlistID, $videosInPlaylist[0]['thumbnail_url']);
                        $result = $playlistDao->getNLatestByCreatorID(10, $userID);
//                        $result = array("Result" => "Successfully removed video from playlist!");
                    }
                }
                else {
                    $result = array("Result" => "Invalid action!");
                }

            } catch (\Exception $e) {
                $result = array("Result" => "Error");
            }
        }
        else {
            $result = array("Result" => "Error");
        }
        $this->jsonEncodeParams($result);
    }
}