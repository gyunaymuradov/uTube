<label class="hiding margin-top"><?= $params['nav_title']; ?></label>

<?php

$users = $params['nav_suggestions'];
/* @var $user \model\User */
foreach ($users as $user) {
    $username = htmlentities($user->getUsername());
    $userId = $user->getId();
    $userPhoto = $user->getUserPhotoUrl();

    echo "<a href='index.php?page=user&id=$userId' id='$userId'><div class='margin-5 width-100 text-left'><img src='$userPhoto' class='img-circle subImg'> <label class='hiding'>&nbsp;&nbsp;$username</label></div></a>";
}

?>