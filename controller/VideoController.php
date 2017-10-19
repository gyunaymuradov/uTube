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

class VideoController extends BaseController {

    public function __construct() {
    }

    public function upload()
    {
        $videoDao = VideoDao::getInstance();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod == 'GET') {
            $tagDao = TagDao::getInstance();
            $tags = $tagDao->getAll();
            $videoId = isset($_GET['id']) ? $_GET['id'] : null;
            $thumbnailUrl = null;
            $title = null;
            $description = null;
            $videoUrl = null;
            $pageTitle = 'Upload video';
            $btnText = 'Upload';
            $previewDivDisplay = 'none';
            $fileInputFieldDisplay = 'block';
            $required = 'required';
            if (!is_null($videoId)) {
                $video = $videoDao->getByID($videoId);
                $thumbnailUrl = $video->getThumbnailURL();
                $title = $video->getTitle();
                $description = $video->getDescription();
                $videoUrl = $video->getVideoURL();
                $pageTitle = 'Edit video';
                $btnText = 'Edit';
                $previewDivDisplay = 'block';
                $fileInputFieldDisplay = 'none';
                $required = '';
            }

            $this->render('video/upload', [
            'tags' => $tags,
            'thumbnailUrl' => $thumbnailUrl,
            'title' => $title,
            'description' => $description,
            'videoUrl' => $videoUrl,
            'pageTitle' => $pageTitle,
            'btnText' => $btnText,
            'previewDivDisplay' => $previewDivDisplay,
            'fileInputDisplay' => $fileInputFieldDisplay,
            'required' => $required

            ]);
        } elseif ($requestMethod == 'POST') {
            if (isset($_SESSION['user']) &&
                isset($_POST["Title"]) &&
                $_POST["Title"] != "" &&
                isset($_POST["Description"]) &&
                $_POST["Description"] != "" &&
                isset($_POST["tags"])) {

                $resultMsg = "Your video was successfully uploaded!";
                $userId = $_SESSION['user']->getId();

                $tmpFileName = $_FILES['videoFile']['tmp_name'];
                $realFileName = $_FILES['videoFile']['name'];
                $fileType = $_FILES['videoFile']['type'];

                if (is_uploaded_file($tmpFileName)) {
                    if (strpos($fileType, "video") === false) {
                        $resultMsg = "Error! File is not a video!";
                    } else {
                        if (!file_exists("../uploads/videos")) {
                            mkdir("../uploads/videos", 0777);
                        }
                        $videoName = "VID_" . $userId . "_" . time();
                        $videoPath = "../uploads/videos/$videoName." . pathinfo($realFileName, PATHINFO_EXTENSION);
                        $thumbPath = "../uploads/thumbnails/$videoName.png";
                        move_uploaded_file($tmpFileName, "$videoPath");
                        if (file_exists($videoPath)) {

                            if (!file_exists("../uploads/thumbnails")) {
                                mkdir("../uploads/thumbnails", 0777);
                            }
                            file_put_contents($thumbPath, file_get_contents("data://".$_POST["Thumbnail"]));
                            $newVideo = new Video(null,
                                $_POST['Title'],
                                $_POST['Description'],
                                date("Y-m-d"),
                                $userId,
                                $videoPath,
                                $thumbPath,
                                $_POST["tags"]
                            );
                            $newVideo->setHidden(0);
                            try {
                                $videoDao->insert($newVideo);
                            } catch (\PDOException $e) {
                                if (file_exists($videoPath)) {
                                    unlink($videoPath);
                                }
                                if (file_exists($thumbPath)) {
                                    unlink($thumbPath);
                                }
                                $resultMsg = "An error occurred! Please try again later.";
                            }
                        }
                    }
                } else {
                    $resultMsg = "Error uploading file!";
                }
                $this->render('video/uploadResult', array("Result" => $resultMsg));
            }
            else {
                header("Location: index.php?page=upload");
            }
        }
    }
    
    public function watchAction() {

        $videoDao = VideoDao::getInstance();
        $playlistDao = PlaylistDao::getInstance();
        $userDao = UserDao::getInstance();

        if (!isset($_GET['playlist-id'])) {
            $videoId = $_GET['id'];
            $video = $videoDao->getByID($videoId);
            $videoUrl = $video->getVideoURL();
            $videoTitle = $video->getTitle();
            $videoDescription = $video->getDescription();
            $dateAdded = $video->getDateAdded();
            $uploaderId = $video->getUploaderID();
            $tagId = $video->getTagId();
            $uploader = $userDao->getById($uploaderId)->getUsername();

            $likes = $videoDao->getLikesCountById($videoId);
            $dislikes = $videoDao->getDislikesCountById($videoId);

            $commentDao = CommentDao::getInstance();
            $comments = $commentDao->getByVideoId($videoId);


            $similarVideos = array();
            $similarVideos = $videoDao->getWithSameTags($tagId, $videoId);
            $sideBarTitle = 'Suggestions';
            $playlistId = null;

        } else {
            $playlistId = $_GET['playlist-id'];
            $playlistContent = $playlistDao->getVideoById($playlistId);
            $videoId = $playlistContent[0]['id'];
            $sideBarTitle = 'Playlist ' . $playlistDao->getByID($playlistId)->getTitle();
            if (isset($_GET['vid-id'])) {
                $videoId = $_GET['vid-id'];
            }
            $video = $videoDao->getByID($videoId);
            $videoUrl = $video->getVideoURL();
            $videoTitle = $video->getTitle();
            $videoDescription = $video->getDescription();
            $dateAdded = $video->getDateAdded();
            $uploaderId = $video->getUploaderID();
            $tagId = $video->getTagId();
            $uploader = $userDao->getById($uploaderId)->getUsername();

            $likes = $videoDao->getLikesCountById($videoId);
            $dislikes = $videoDao->getDislikesCountById($videoId);

            $commentDao = CommentDao::getInstance();
            $comments = $commentDao->getByVideoId($videoId);

            $similarVideos = $playlistContent;
        }

        $logged = 'false';
        $loggedUserId = null;

        if (isset($_SESSION['user'])) {
            /* @var $loggedUser User */
            $loggedUser = $_SESSION['user'];
            $loggedUserId = $loggedUser->getId();
            $logged = 'true';
        }

        $this->render('video/watch', [
            'uploaderId' => $uploaderId,
            'videoId' => $videoId,
            'videoUrl' => $videoUrl,
            'videoTitle' => $videoTitle,
            'videoDescription' => $videoDescription,
            'dateAdded' => $dateAdded,
            'uploader' => $uploader,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'loggedUserId' => $loggedUserId,
            'logged' => $logged,
            'comments' => $comments,
            'suggestedVideos' => $similarVideos,
            'sidebarTitle' => $sideBarTitle,
            'playlistId' => $playlistId
        ]);
    }

    public function deleteAction () {
        if (isset($_GET['videoId'])) {
            try {
                $videoDao = VideoDao::getInstance();
                $videoDao->delete($_GET['videoId']);
                $result = "Success";
            }
            catch (\PDOException $e) {
                $result = "An error occurred! Please Try Again Later!";
            }
            $this->jsonEncodeParams(["Result" => $result]);
        }
    }

    public function likeDislikeVideoAction() {
        $videoId = $_GET['video-id'];
        $userId = $_GET['user-id'];
        $likeDislike = $_GET['like'];
        $videoDao = VideoDao::getInstance();
        if ($likeDislike == '1') {
            $videoDao->like($videoId, $userId);
        } else {
            $videoDao->dislike($videoId, $userId);
        }

        $likes = $videoDao->getLikesCountById($videoId);
        $dislikes = $videoDao->getDislikesCountById($videoId);

        $this->jsonEncodeParams([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }

    public function likeDislikeCommentAction() {
        $commentId = $_GET['comment-id'];
        $userId = $_GET['user-id'];
        $likeDislike = $_GET['like'];
        $commentDao = CommentDao::getInstance();
        if ($likeDislike == '1') {
            $commentDao->like($commentId, $userId);
        } else {
            $commentDao->dislike($commentId, $userId);
        }

        $likes = $commentDao->getLikesCount($commentId);
        $dislikes = $commentDao->getDislikesCount($commentId);

        $this->jsonEncodeParams([
            'likes' => $likes,
            'dislikes' => $dislikes
        ]);
    }

    public function comment() {
        $commentText = $_POST['comment'];
        $videoId = $_POST['videoId'];
        $userId = $_POST['userId'];
        $date = date('Y-m-d');
        $comment = new Comment($videoId, $userId, $commentText, $date);

        $commentDao = CommentDao::getInstance();
        $lastInsertId = $commentDao->add($comment);

        /* @var $user User */
        $user = $_SESSION['user'];
        $username = $user->getUsername();
        $userPhotoUrl = $user->getUserPhotoUrl();

        $this->jsonEncodeParams([
            'comment' => $commentText,
            'date' => $date,
            'username' => $username,
            'userId' => $userId,
            'userPhoto' => $userPhotoUrl,
            'commentId' => $lastInsertId,
            'likes' => '0',
            'dislikes' => '0'
        ]);
    }
}
