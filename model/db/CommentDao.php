<?php

namespace model\db;


use model\Comment;
use \PDO;

class CommentDao {

    private static $instance;
    private $pdo;

    const ADD = "INSERT INTO video_comments (video_id, user_id, text, date_added) VALUES (?, ?, ?, ?)";
    const GET_BY_VIDEO_ID = "SELECT u.username, u.id as user_id, c.id, c.text, c.date_added FROM users u JOIN video_comments c ON u.id = c.user_id WHERE c.video_id = ? ORDER BY c.date_added DESC";
    const CHECK_IF_LIKED_OR_DISLIKED = "SELECT likes FROM comments_likes_dislikes WHERE comment_id = ? AND user_id = ?";
    const LIKE = "INSERT INTO comments_likes_dislikes (comment_id, user_id, likes) VALUES (?, ?, 1)";
    const UNLIKE = "DELETE FROM comments_likes_dislikes WHERE comment_id = ? AND user_id = ?";
    const DISLIKE = "INSERT INTO comments_likes_dislikes (comment_id, user_id, likes) VALUES (?, ?, 0)";
    const UNDISLIKE = "DELETE FROM comments_likes_dislikes WHERE comment_id = ? AND user_id = ?";
    const UPDATE_LIKE_DISLIKE = "UPDATE comments_likes_dislikes SET likes = ? WHERE comment_id = ? AND user_id = ?";
    const GET_LIKES_COUNT = "SELECT COUNT(*) as likes_count FROM comments_likes_dislikes WHERE comment_id = ? AND likes = 1";
    const GET_DISLIKES_COUNT = "SELECT COUNT(*) as dislikes_count FROM comments_likes_dislikes WHERE comment_id = ? AND likes = 0";

    private function __construct() {
        $this->pdo = DBManager::getInstance()->dbConnect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new CommentDao();
        }
        return self::$instance;
    }

    /**
     * @param Comment $comment
     * @return bool
     */
    public function add(Comment $comment) {
        $statement = $this->pdo->prepare(self::ADD);
        $result = $statement->execute(array($comment->getVideoId(), $comment->getUserId(), $comment->getText(), $comment->getDateAdded()));
        return $result;
    }

    /**
     * @param int $id
     * @return array of Comment objects
     */
    public function getByVideoId($videoId) {
        $statement = $this->pdo->prepare(self::GET_BY_VIDEO_ID);
        $statement->execute(array($videoId));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // check if there are any comments returned and if so add them to array of objects
        if (!empty($result)) {
            $comments = array();
            foreach ($result as $currentComment) {
                $comment = new Comment($videoId, $currentComment['user_id'], $currentComment['text'], $currentComment['date_added'], $currentComment['username']);
                $comment->setId($currentComment['id']);
                $likes = $this->getLikesCount($currentComment['id']);
                $dislikes = $this->getDislikesCount($currentComment['id']);
                $comment->setLikes($likes);
                $comment->setDislikes($dislikes);
                $comments[] = $comment;
            }
            return $comments;
        }
    }

    /**
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function checkIfLikedOrDisliked($commentId, $userId) {
        //check if this comment has either like or dislike from the current user
        $statement = $this->pdo->prepare(self::CHECK_IF_LIKED_OR_DISLIKED);
        $statement->execute(array($commentId, $userId));
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (isset($result['likes'])) {
            if ($result['likes'] == 1) {
                return true;
            } else if ($result['likes'] == 0) {
                return false;
            }
        } else {
            return null;
        }
    }

    /**
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function like($commentId, $userId) {
        $likes = $this->checkIfLikedOrDisliked($commentId, $userId);

        //if comment is not liked on pressing button 'like' like is added
        if (is_null($likes)) {
            $statement = $this->pdo->prepare(self::LIKE);
            $statement->execute(array($commentId, $userId));
        } else if ($likes == true) {
            //if already liked on pressing button 'like' again the like is removed
            $statement = $this->pdo->prepare(self::UNLIKE);
            $statement->execute(array($commentId, $userId));
        } else {
            $statement = $this->pdo->prepare(self::UPDATE_LIKE_DISLIKE);
            $statement->execute(array('1', $commentId, $userId));
        }
    }

    /**
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function dislike($commentId, $userId) {
        $likes = $this->checkIfLikedOrDisliked($commentId, $userId);

        //if comment is not disliked on pressing button 'dislike' dislike is added
        if (is_null($likes)) {
            $statement = $this->pdo->prepare(self::DISLIKE);
            $statement->execute(array($commentId, $userId));
        } else if ($likes == false) {
            //if already disliked on pressing button 'dislike' again the dislike is removed
            $statement = $this->pdo->prepare(self::UNDISLIKE);
            $statement->execute(array($commentId, $userId));
        } else {
            $statement = $this->pdo->prepare(self::UPDATE_LIKE_DISLIKE);
            $statement->execute(array('0', $commentId, $userId));
        }
    }

    /**
     * @param int $commentId
     * @return mixed
     */
    public function getLikesCount($commentId) {
        $statement = $this->pdo->prepare(self::GET_LIKES_COUNT);
        $statement->execute(array($commentId));
        $result = $statement->fetch(PDO::FETCH_ASSOC)['likes_count'];
        return $result;
    }

    /**
     * @param int $commentId
     * @return mixed
     */
    public function getDislikesCount($commentId) {
        $statement = $this->pdo->prepare(self::GET_DISLIKES_COUNT);
        $statement->execute(array($commentId));
        $result = $statement->fetch(PDO::FETCH_ASSOC)['dislikes_count'];
        return $result;
    }
}