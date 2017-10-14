<?php require_once 'components/header.php'; ?>
<?php require_once 'components/nav.php'; ?>



<?php

/* @var $user \model\User */
$user = $_SESSION['user'];
$userPhoto = $user->getUserPhotoUrl();
$firstName = $user->getFirstName();
$lastName = $user->getLastName();
$username = $user->getUsername();
$email = $user->getEmail();
$subscribersCount = $user->getSubscribers();
$subscriptionsCount = $user->getSubscriptions();

/* @var $userDao \model\db\UserDao */
$userDao = \model\db\UserDao::getInstance();
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

        <div class="col-md-7 col-md-offset-1">
            <div class="col-md-4"><h3 class="text-muted">Username: </h3></div>
            <div class="col-md-8"><h3 class="text-muted"><?= $username; ?></h3></div>
            <div class="col-md-4"><h3 class="text-muted">Name: </h3></div>
            <div class="col-md-8"><h3 class="text-muted"><?=  $firstName . ' ' . $lastName; ?></h3></div>
            <div class="col-md-4"><h3 class="text-muted">Email: </h3></div>
            <div class="col-md-8"><h3 class="text-muted"><?= $email; ?></h3></div>
            <div class="col-md-4"><h3 class="text-muted">Subscribers: </h3></div>
            <div class="col-md-8"><h3 class="text-muted"><?= $subscribersCount; ?></h3></div>
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
                        <h3>My videos</h3>
                        <p>Some content.</p>
                    </div>
                    <div id="menu1" class="tab-pane fade">
                        <h3>Menu 1</h3>
                        <p>Some content in menu 1.</p>
                    </div>
                    <div id="menu2" class="tab-pane fade">
                        <h3>Menu 2</h3>
                        <p>Some content in menu 2.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php require_once 'components/footer.php'; ?>