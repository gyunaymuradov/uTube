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

    public function uploadAction()
    {
        $videoDao = VideoDao::getInstance();
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        if ($requestMethod == 'GET') {
            $tagDao = TagDao::getInstance();
            $tags = $tagDao->getAll();
            $thumbnailUrl = null;
            $title = null;
            $description = null;
            $videoUrl = null;
            $pageTitle = 'Upload video';
            $btnText = 'Upload';
            $previewDivDisplay = 'none';
            $fileInputFieldDisplay = 'block';
            $required = 'required';
            $formAction = 'index.php?page=upload';
            $selected = 'Other';
            $this->render('video/upload', [
                'tags' => $tags,
                'thumbnail_url' => $thumbnailUrl,
                'title' => $title,
                'description' => $description,
                'video_url' => $videoUrl,
                'page_title' => $pageTitle,
                'btn_text' => $btnText,
                'preview_div_display' => $previewDivDisplay,
                'file_input_display' => $fileInputFieldDisplay,
                'required' => $required,
                'form_action' => $formAction,
                'selected' => $selected
            ]);
        } elseif ($requestMethod == 'POST') {
            if (isset($_SESSION['user']) &&
                isset($_POST['title']) &&
                isset($_POST['description'])) {

                $resultMsg = 'Your video was successfully uploaded!';
                $userId = $_SESSION['user']->getId();

                $tmpFileName = $_FILES['video-file']['tmp_name'];
                $realFileName = $_FILES['video-file']['name'];
                $fileType = $_FILES['video-file']['type'];
                $videoId = '';
                if (is_uploaded_file($tmpFileName)) {
                    if (strpos($fileType, 'video') === false) {
                        $resultMsg = 'Error! File is not a video!';
                    } else {
                        if (!file_exists('../uploads/videos')) {
                            mkdir('../uploads/videos', 0777);
                        }
                        $videoName = 'VID_' . $userId . "_" . time();
                        $videoPath = "../uploads/videos/$videoName." . pathinfo($realFileName, PATHINFO_EXTENSION);
                        $thumbPath = "../uploads/thumbnails/$videoName.png";
                        move_uploaded_file($tmpFileName, $videoPath);
                        if (file_exists($videoPath)) {

                            if (!file_exists('../uploads/thumbnails')) {
                                mkdir('../uploads/thumbnails', 0777);
                            }
                            file_put_contents($thumbPath, file_get_contents('data://' . $_POST['thumbnail']));
                            $newVideo = new Video(
                                null,
                                $_POST['title'],
                                $_POST['description'],
                                date('Y-m-d'),
                                $userId,
                                $videoPath,
                                $thumbPath,
                                $_POST['tags'],
                                '0'
                            );
                            try {
                                $videoId = $videoDao->insert($newVideo);
                            } catch (\PDOException $e) {
                                if (file_exists($videoPath)) {
                                    unlink($videoPath);
                                }
                                if (file_exists($thumbPath)) {
                                    unlink($thumbPath);
                                }
                                $resultMsg = 'An error occurred! Please try again later.';
                            }
                        }
                    }
                } else {
                    $resultMsg = 'Error uploading file!';
                }
                $this->render('video/upload-result',[
                    'result' => $resultMsg,
                    'video_id' => $videoId
                ]);
            }
            else {
                header('Location: index.php?page=upload');
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
            'uploader_id' => $uploaderId,
            'video_id' => $videoId,
            'video_url' => $videoUrl,
            'video_title' => $videoTitle,
            'video_description' => $videoDescription,
            'date_added' => $dateAdded,
            'uploader' => $uploader,
            'likes' => $likes,
            'dislikes' => $dislikes,
            'logged_user_id' => $loggedUserId,
            'logged' => $logged,
            'comments' => $comments,
            'suggested_videos' => $similarVideos,
            'sidebar_title' => $sideBarTitle,
            'playlist_id' => $playlistId
        ]);
    }

    public function deleteAction() {
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

    public function commentAction() {
        $commentText = $_POST['comment'];
        $videoId = $_POST['video-id'];
        $userId = $_POST['user-id'];
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
            'user_id' => $userId,
            'user_photo' => $userPhotoUrl,
            'comment_id' => $lastInsertId,
            'likes' => '0',
            'dislikes' => '0'
        ]);
    }

    public function editAction() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $videoDao = VideoDao::getInstance();
        if ($requestMethod == 'GET') {
            $tagDao = TagDao::getInstance();
            $tags = $tagDao->getAll();
            $videoId = $_GET['id'];
            $video = $videoDao->getByID($videoId);
            $thumbnailUrl = $video->getThumbnailURL();
            $title = $video->getTitle();
            $description = $video->getDescription();
            $videoUrl = $video->getVideoURL();
            $tagId = $video->getTagId();
            $pageTitle = 'Edit video';
            $btnText = 'Edit';
            $previewDivDisplay = 'block';
            $fileInputFieldDisplay = 'none';
            $required = '';
            $formAction = 'index.php?page=edit-video';

            $this->render('video/upload', [
                'video_id' => $videoId,
                'tags' => $tags,
                'thumbnail_url' => $thumbnailUrl,
                'title' => $title,
                'description' => $description,
                'video_url' => $videoUrl,
                'page_title' => $pageTitle,
                'btn_text' => $btnText,
                'preview_div_display' => $previewDivDisplay,
                'file_input_display' => $fileInputFieldDisplay,
                'required' => $required,
                'form_action' => $formAction,
                'tag' => $tagId
            ]);
        } else if ($requestMethod == 'POST' &&
                    isset($_SESSION['user']) &&
                    isset($_POST['video-id']) &&
                    isset($_POST['title']) &&
                    isset($_POST['description']) &&
                    isset($_POST['old-thumbnail-url'])) {

            $thumbnailURL = $_POST['old-thumbnail-url'];
            if (file_exists($thumbnailURL) && $_POST['thumbnail'] != '') {
                unlink($thumbnailURL);
                file_put_contents($thumbnailURL, file_get_contents('data://' . $_POST['thumbnail']));
            }
            $editedVideo = new Video($_POST['video-id'], $_POST['title'], $_POST['description'], null, null, null, null, $_POST['tags'], '0');
            try {
                $videoDao = VideoDao::getInstance();
                $rowsAffected = $videoDao->edit($editedVideo);
                $resultMsg = 'Video edited successfully!';
                if ($rowsAffected == 1) {
                    $this->render('video/upload-result', [
                        'result' => $resultMsg,
                        'video_id' => $_POST['video-id']
                    ]);
                }
            }
            catch (\PDOException $e) {
                $resultMsg = 'An error occurred! Please try again later!';
            }

        }
    }
}
