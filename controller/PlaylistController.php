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
        catch (\PDOException $e) {
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
                $result = array("Result" => "Success");
            }
            else {
                $result = array("Result" => "Error! You cant leave empty fields!");
            }
        }
        catch (\PDOException $e) {
            $result = array("Result" => "Error! Please try again later!");
        }
        $this->jsonEncodeParams($result);
    }

    public function insertVideo() {
        try {
            if (isset($_GET['playlistID']) && $_GET['playlistID'] != "" && isset($_GET['videoID']) && $_GET['videoID'] != "") {
                $playlistDao = PlaylistDao::getInstance();
                $playlistID = htmlspecialchars($_GET['playlistID']);
                $videoID = htmlspecialchars($_GET['videoID']);
                $playlistDao->insertVideo($playlistID, $videoID);
                $result = array("Result" => "Success");
            }
            else {
                $result = array("Result" => "Error! You cant leave empty fields!");
            }
        }
        catch (\PDOException $e) {
            $result = array("Result" => "Error! Please try again later!");
        }
        $this->jsonEncodeParams($result);
    }
}