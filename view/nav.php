<div id="main" class="row flex">
    <div id="navbar" class="col-md-2 bg-info justify-content-center text-center navbar">
        <a href="index.php"><button class="btn btn-info btn-md col-lg-12 margin-5"><span class="glyphicon glyphicon-home"></span> <span class="hiding">Home</span></button></a>
        <div id="subscribes-section">
            <label class="hiding margin-top"><?= $params['nav_title']; ?></label>

            <?php

            $users = $params['nav_suggestions'];
            /* @var $user \model\User */
            foreach ($users as $user) {
                $username = htmlentities($user->getUsername());
                $userId = $user->getId();
                $userPhoto = $user->getUserPhotoUrl();

                echo "<a href='index.php?page=user&action=user&id=$userId' id='$userId'><div class='margin-5 width-100 text-left'><img src='$userPhoto' class='img-circle subImg'> <label class='hiding'>&nbsp;&nbsp;$username</label></div></a>";
            }

            ?>

        </div>
    </div>
