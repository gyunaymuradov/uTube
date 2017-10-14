<?php require_once 'components/header.php'; ?>
<?php require_once 'components/nav.php'; ?>



<?php

/* @var $user \model\User */
$user = $_SESSION['user'];
$userPhoto = $user->getUserPhotoUrl();
$firstName = $user->getFirstName();
$lastName = $user->getLastName();
$username = $user->getUsername();
$userId = $user->getId();
$email = $user->getEmail();
$dateJoined = $user->getDateJoined();

/* @var $userDao \model\db\UserDao */
$userDao = \model\db\UserDao::getInstance();

/* @var $videoDao \model\db\VideoDao */
$videoDao = \model\db\VideoDao::getInstance();


$videos = $videoDao->getNLatestByUploaderID(10, $userId);

$subscribersCount = $userDao->getSubscribersCount($userId);
$subscriptionsCount = $userDao->getSubscriptionsCount($userId);

$visible = true;
if ($_GET['id'] == $user->getId()) {
    $visible = false;
} else {
    if (!isset($_SESSION['user'])) {
        $class = 'disabled';
    }
}


?>



    <div class="col-md-10">
        <div class="row">
            <div class="col-md-2 col-md-offset-1">
                <img src="<?= '../' . $userPhoto; ?>" alt="" width="250" class="img-rounded" height="auto">
            </div>
            <div class="col-md-4 col-md-offset-2">
                <h3 class="text-muted"><?= $username; ?></h3>
            </div>
            <div class="col-md-4 col-md-offset-2">
                <h3 class="text-muted"><?= $subscribersCount; ?> <small> subscribers</small></h3>
            </div>
        </div>
        <div class="row margin-top">
            <div class="col-md-11 col-md-offset-1"">
                <ul class="nav nav-tabs nav-justified">
                    <li class="active"><a data-toggle="tab" href="#home">Videos</a></li>
                    <li><a data-toggle="tab" href="#menu1">Playlists</a></li>
                    <li><a data-toggle="tab" href="#menu2">About</a></li>
                </ul>
                <div class="tab-content container-fluid">
                    <div id="home" class="tab-pane fade in active">
                        <?php

                        /* @var $video \model\Video */
                        foreach ($videos as $video) {
                            $title = $video->getTitle();
                            $thumbnail = $video->getThumbnailURL();
                            $videoId = $video->getId();
                            echo "
                        <div class=\"col-md-3 margin-top\">
                        <a href='watch.php?id=$videoId'>
                            <img src=\" ../$thumbnail\" class=\"img - rounded\" alt=\"\" width=\"200\" height=\"auto\">
                            <h4 class='text-center text-muted'>$title</h4>
                            </a>
                        </div>";
                        }

                        ?>
                    </div>
                    <div id="menu1" class="tab-pane fade">
                        <h3>Menu 1</h3>
                        <p>Some content in menu 1.</p>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Name: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?=  $firstName . ' ' . $lastName; ?></h3>
                        </div>
                        <div class="col-md-1 margin-top">
                            <button class="btn btn-info" onclick="getEditForm(<?= $userId ?>)">Edit profile</button>
                        </div>
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Email: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?= $email ?></h3>
                        </div>
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Member since: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?= $dateJoined; ?></h3>
                        </div>
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Subscriptions: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?=  $subscriptionsCount; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>


<?php require_once 'components/footer.php'; ?>