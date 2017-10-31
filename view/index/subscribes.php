<label class="hiding margin-top"><?= $params['nav_title']; ?></label>

<?php

$users = $params['nav_suggestions'];
/* @var $user \model\User */
foreach ($users as $user) {
    $username = htmlentities($user->getUsername());
    $userId = $user->getId();
    $userPhoto = $user->getUserPhotoUrl();

    echo "<a href='index.php?controller=video&action=user&id=$userId' id='$userId'><div class='margin-5 width-100 text-left'><img src='$userPhoto' class='img-circle subImg'> <label class='hiding'>&nbsp;&nbsp;$username</label></div></a>";
}

if (count($params['subscribers']) > 0) {
    echo "<h3></h3><label class='hiding margin-top'>Your subscribers:</label>";
    foreach ($params['subscribers'] as  $subscriber) {
        $subscriberId = $subscriber['id'];
        $subscriberUsername = $subscriber['username'];
        $subscriberPhoto = $subscriber['user_photo_url'];
        echo "<a href='index.php?controller=user&action=user&id=$subscriberId' id='$subscriberId'><div class='margin-5 width-100 text-left'><img src='$subscriberPhoto' class='img-circle subImg'> <label class='hiding'>&nbsp;&nbsp;$subscriberUsername</label></div></a>";
    }
}
?>