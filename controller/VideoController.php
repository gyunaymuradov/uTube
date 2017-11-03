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

    public function upload() {
        try {
            $videoDao = VideoDao::getInstance();
            $tagDao = TagDao::getInstance();
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $tags = $tagDao->getAll();
            $thumbnailUrl = null;
            $title = null;
            $description = null;
            $videoUrl = null;
            $selected = 16;

            if ($requestMethod == 'GET') {
                $this->render('video/upload', [
                    'tags' => $tags,
                    'thumbnail_url' => $thumbnailUrl,
                    'title' => $title,
                    'description' => $description,
                    'video_url' => $videoUrl,
                    'tag' => $selected
                ]);
            } elseif ($requestMethod == 'POST') {
                if (isset($_SESSION['user']) &&
                    isset($_POST['title']) &&
                    isset($_POST['description'])) {

                    $title = $_POST['title'];
                    $description = $_POST['description'];
                    $tmpFileName = $_FILES['video-file']['tmp_name'];
                    $realFileName = $_FILES['video-file']['name'];
                    $tagId = $_POST['tags'];
                    $userId = $_SESSION['user']->getId();

                    $resultMsg = 'Your video was successfully uploaded!';
                    $videoId = '';
                    $errors = array();

                    $validator = Validator::getInstance();
                    $validTitle = $validator->validateTitle($title);
                    if ($videoDao->checkTitleExists($title, $userId)) {
                        $errors['title'][] = "A video with this title already exists.";
                    }
                    $validDescription = $validator->validateDescription($description);
                    $extensions = ['mp4', 'webm', 'ogg'];
                    $validVideo = $validator->validateUploadedVideo($realFileName, $tmpFileName, 52428800, 'video', $extensions);

                    if (is_array($validTitle)) {
                        foreach ($validTitle as $error) {
                            $errors['title'][] = $error;
                        }
                    }
                    if (is_array($validDescription)) {
                        foreach ($validDescription as $error) {
                            $errors['description'][] = $error;
                        }
                    }
                    if (is_array($validVideo)) {
                        foreach ($validVideo as $error) {
                            $errors['video'][] = $error;
                        }
                    }

                    if (empty($errors)) {
                        if (is_uploaded_file($tmpFileName)) {
                            if (!file_exists('uploads/videos')) {
                                mkdir('uploads/videos', 0777);
                            }
                            $videoName = 'VID_' . $userId . "_" . time();
                            $videoPath = "uploads/videos/$videoName." . pathinfo($realFileName, PATHINFO_EXTENSION);
                            $thumbPath = "uploads/thumbnails/$videoName.png";
                            move_uploaded_file($tmpFileName, $videoPath);
                            if (!file_exists('uploads/thumbnails')) {
                                mkdir('uploads/thumbnails', 0777);
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
                                $tagId,
                                '0'
                            );
                            try {
                                $videoId = $videoDao->insert($newVideo);
                                $this->render('video/upload-result', [
                                    'result' => $resultMsg,
                                    'video_id' => $videoId,
                                    'success' => 1
                                ]);
                            } catch (\Exception $e) {
                                if (file_exists($videoPath)) {
                                    unlink($videoPath);
                                }
                                if (file_exists($thumbPath)) {
                                    unlink($thumbPath);
                                }
                                $resultMsg = 'An error occurred! Please try again later.';
                                $this->render('video/upload-result', [
                                    'result' => $resultMsg,
                                    'video_id' => $videoId,
                                    'success' => 0
                                ]);
                            }
                        } else {
                            $resultMsg = 'Error uploading file!';
                            $this->render('video/upload-result', [
                                'result' => $resultMsg,
                                'video_id' => $videoId,
                                'success' => 0
                            ]);
                        }
                    } else {
                        $this->render('video/upload', [
                            'tags' => $tags,
                            'thumbnail_url' => $thumbnailUrl,
                            'title' => $title,
                            'description' => $description,
                            'video_url' => $videoUrl,
                            'tag' => $tagId,
                            'errors' => $errors
                        ]);
                    }

                }
            }
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }
    
    public function watch() {
        try {
            $videoDao = VideoDao::getInstance();
            $playlistDao = PlaylistDao::getInstance();
            $userDao = UserDao::getInstance();

            if (!isset($_GET['playlist-id'])) {
                $videoId = $_GET['id'];
                $video = $videoDao->getByID($videoId);
                if (is_null($video)) {
                    header('Location:index.php');
                }
                $videoUrl = $video->getVideoURL();
                $videoTitle = $video->getTitle();
                $videoDescription = $video->getDescription();
                $dateAdded = $video->getDateAdded();
                $uploaderId = $video->getUploaderID();
                $tagId = $video->getTagId();
                $thumbnailUrl = $video->getThumbnailURL();
                $uploader = $userDao->getById($uploaderId)->getUsername();

                $likes = $videoDao->getLikesCountById($videoId);
                $dislikes = $videoDao->getDislikesCountById($videoId);

                $commentDao = CommentDao::getInstance();
                $comments = $commentDao->getByVideoId($videoId, 4, 0);


                $similarVideos = array();
                $similarVideos = $videoDao->getWithSameTags($tagId, $videoId);
                $sideBarTitle = 'Suggestions';
                $playlistId = null;
                $playistCreatorId = null;

            } else {
                $playlistId = $_GET['playlist-id'];
                $playlistContent = $playlistDao->getVideoById($playlistId);
                if (is_null($playlistContent)) {
                    header('Location:index.php');
                }
                $videoId = $playlistContent[0]['id'];
                if (!isset($playlistContent[0])) {
                    header('Location:index.php');
                }
                $sideBarTitle = 'Playlist ' . $playlistDao->getByID($playlistId)->getTitle();
                if (isset($_GET['vid-id'])) {
                    $videoId = $_GET['vid-id'];
                }
                $video = $videoDao->getByID($videoId);
                if (is_null($video)) {
                    header('Location:index.php');
                }
                $playistCreatorId = $playlistDao->getByID($playlistId)->getCreatorID();
                $videoUrl = $video->getVideoURL();
                $thumbnailUrl = $video->getThumbnailURL();
                $videoTitle = $video->getTitle();
                $videoDescription = $video->getDescription();
                $dateAdded = $video->getDateAdded();
                $uploaderId = $video->getUploaderID();
                $tagId = $video->getTagId();
                $uploader = $userDao->getById($uploaderId)->getUsername();

                $likes = $videoDao->getLikesCountById($videoId);
                $dislikes = $videoDao->getDislikesCountById($videoId);

                $commentDao = CommentDao::getInstance();
                $comments = $commentDao->getByVideoId($videoId, 4, 0);

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
                'thumbnail_url' => $thumbnailUrl,
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
                'playlist_id' => $playlistId,
                'playlistCreatorId' => $playistCreatorId
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function delete() {
        if (isset($_GET['videoId']) && isset($_SESSION['user'])) {
            try {
                $videoDao = VideoDao::getInstance();
                $videoId = htmlspecialchars($_GET['videoId']);
                $videoFromDb = $videoDao->getByID($videoId);
                $userID = $_SESSION['user']->getId();
                if ($userID == $videoFromDb->getUploaderID() && !is_null($videoFromDb)) {
                    $videoDao->delete($videoId);
                    $result = "Success";
                }
                else {
                    $result = array("Result" => "Invalid action!");
                }
            }
            catch (\Exception $e) {
                $result = "An error occurred! Please Try Again Later!";
            }
            $this->jsonEncodeParams(["Result" => $result]);
        }
    }

    public function likeDislikeVideo() {
        try {
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
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function likeDislikeComment() {
        try {
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
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function comment() {
        try {
            $commentText = $_POST['comment'];
            $videoId = $_POST['video-id'];
            $userId = $_POST['user-id'];
            $date = date('Y-m-d');
            $comment = new Comment($videoId, $userId, $commentText, $date);

            $commentDao = CommentDao::getInstance();
            $lastInsertId = $commentDao->add($comment);

            $comments = $commentDao->getByVideoId($videoId, 4, 0);
            $this->renderPartial('video/comments',[
               'comments' => $comments
            ]);
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function edit() {
        try {
            $requestMethod = $_SERVER['REQUEST_METHOD'];
            $videoDao = VideoDao::getInstance();
            $tagDao = TagDao::getInstance();
            $tags = $tagDao->getAll();
            $videoId = isset($_GET['id']) ? $_GET['id'] : $_POST['video-id'];
            $video = $videoDao->getByID($videoId);
            $thumbnailUrl = $video->getThumbnailURL();
            $title = $video->getTitle();
            $description = $video->getDescription();
            $videoUrl = $video->getVideoURL();
            $tagId = $video->getTagId();

            if ($requestMethod == 'GET') {
                $this->render('video/edit', [
                    'video_id' => $videoId,
                    'tags' => $tags,
                    'thumbnail_url' => $thumbnailUrl,
                    'title' => $title,
                    'description' => $description,
                    'video_url' => $videoUrl,
                    'tag' => $tagId
                ]);
            } else if ($requestMethod == 'POST' &&
                isset($_SESSION['user']) &&
                isset($_POST['video-id']) &&
                isset($_POST['title']) &&
                isset($_POST['description']) &&
                isset($_POST['old-thumbnail-url'])) {

                $validator = Validator::getInstance();
                $title = $_POST['title'];
                $description = $_POST['description'];
                $validTitle = $validator->validateTitle($title);
                $validDescription = $validator->validateDescription($description);

                $errors = array();
                if (is_array($validTitle)) {
                    foreach ($validTitle as $error) {
                        $errors['title'][] = $error;
                    }
                }
                if (is_array($validDescription)) {
                    foreach ($validDescription as $error) {
                        $errors['description'][] = $error;
                    }
                }

                if (empty($errors)) {
                    $thumbnailURL = $_POST['old-thumbnail-url'];
                    if (file_exists($thumbnailURL) && $_POST['thumbnail'] != '') {
                        unlink($thumbnailURL);
                        file_put_contents($thumbnailURL, file_get_contents('data://' . $_POST['thumbnail']));
                    }
                    $editedVideo = new Video($_POST['video-id'], $_POST['title'], $_POST['description'], null, null, null, null, $_POST['tags'], '0');
                    try {
                        $videoDao->edit($editedVideo);
                        $resultMsg = 'Video edited successfully!';
                        $this->render('video/upload-result', [
                            'result' => $resultMsg,
                            'video_id' => $_POST['video-id']
                        ]);
                    } catch (\Exception $e) {
                        $resultMsg = 'An error occurred! Please try again later!';
                        $this->render('video/upload-result', [
                            'result' => $resultMsg,
                            'video_id' => $_POST['video-id'],
                            'success' => 0
                        ]);
                    }
                } else {
                    $this->render('video/upload', [
                        'video_id' => $videoId,
                        'tags' => $tags,
                        'thumbnail_url' => $thumbnailUrl,
                        'title' => $_POST['title'],
                        'description' => $_POST['description'],
                        'video_url' => $videoUrl,
                        'tag' => $tagId,
                        'errors' => $errors
                    ]);
                }

            }
        }
        catch (\Exception $e) {
            $this->render('index/error');
        }
    }

    public function loadComments() {
        $offset = $_GET['start'];
        $videoId = $_GET['video-id'];
        $commentDao = CommentDao::getInstance();
        $comments = $commentDao->getByVideoId($videoId, 4, $offset);
        $this->renderPartial('video/comments', [
            'comments' => $comments
        ]);
    }

}
