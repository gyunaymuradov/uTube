<?php

namespace model\db;


use model\Comment;

class CommentDao {
    private static $instance;
    private $pdo;

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
    public function addComment(Comment $comment) {
        $statement = $this->pdo->prepare("INSERT INTO video_comments (video_id, user_id, text, date_added) VALUES (?, ?, ?, ?)");
        $result = $statement->execute(array($comment->getVideoId(), $comment->getUserId(), $comment->getText(), $comment->getDateAdded()));
        return $result;
    }

    /**
     * @param int $id
     * @return array of Comment objects
     */
    public function getCommentsByVideoId($id) {
        $statement = $this->pdo->prepare("SELECT u.username, c.id, c.text, c.date_added FROM users u JOIN video_comments c ON u.id = c.user_id WHERE c.video_id = ? ORDER BY c.date_added DESC");
        $statement->execute(array($id));
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);

        // check if there are any comments returned and if so add them to array of objects
        if (!empty($result)) {
            $comments = array();
            foreach ($result as $currentComment) {
                $comment = new Comment();
                $comment->setId($currentComment['id']);
                $comment->setCreatorUsername($currentComment['username']);
                $comment->setText($currentComment['text']);
                $comment->setDateAdded($currentComment['date_added']);
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
    public function checkIfCommentIsLikedOrDislikedByUser($commentId, $userId) {
        //check if this comment has either like or dislike from the current user
        $statement = $this->pdo->prepare("SELECT likes FROM comments_likes_dislikes WHERE comment_id = ? AND user_id = ?");
        $statement->execute(array($commentId, $userId));
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
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
    public function likeComment($commentId, $userId) {
        $likes = $this->checkIfCommentIsLikedOrDislikedByUser($commentId, $userId);

        //if comment is not liked on pressing button 'like' like is added
        if ($likes == null) {
            $statement = $this->pdo->prepare("INSERT INTO comments_likes_dislikes (comment_id, user_id, likes) VALUES (?, ?, 1)");
            $result = $statement->execute(array($commentId, $userId));
            return $result;
        } else if ($likes == true) {
            //if already liked on pressing button 'like' again the like is removed
            $statement = $this->pdo->prepare("DELETE FROM comments_likes_dislikes WHERE comment_id = ? AND user_id = ?");
            $result = $statement->execute(array($commentId, $userId));
            return $result;
        }
    }

    /**
     * @param int $commentId
     * @param int $userId
     * @return bool
     */
    public function dislikeComment($commentId, $userId) {
        $likes = $this->checkIfCommentIsLikedOrDislikedByUser($commentId, $userId);

        //if comment is not disliked on pressing button 'dislike' dislike is added
        if ($likes == null) {
            $statement = $this->pdo->prepare("INSERT INTO comments_likes_dislikes (comment_id, user_id, likes) VALUES (?, ?, 0)");
            $result = $statement->execute(array($commentId, $userId));
            return $result;
        } else if ($likes == true) {
            //if already disliked on pressing button 'dislike' again the dislike is removed
            $statement = $this->pdo->prepare("DELETE FROM comments_likes_dislikes WHERE comment_id = ? AND user_id = ?");
            $result = $statement->execute(array($commentId, $userId));
            return $result;
        }
    }

    public function getCommentLikesCountByCommentId($commentId) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as likes_count FROM comments_likes_dislikes WHERE comment_id = ? AND likes = 1");
        $statement->execute(array($commentId));
        $result = $statement->fetch(\PDO::FETCH_ASSOC)['likes_count'];
        return $result;
    }

    public function getCommentDislikesCountByCommentId($commentId) {
        $statement = $this->pdo->prepare("SELECT COUNT(*) as dislikes_count FROM comments_likes_dislikes WHERE comment_id = ? AND likes = 0");
        $statement->execute(array($commentId));
        $result = $statement->fetch(\PDO::FETCH_ASSOC)['dislikes_count'];
        return $result;
    }
}