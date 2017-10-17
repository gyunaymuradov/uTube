<div class="row">
    <div id="navbar" class="col-md-2 bg-info justify-content-center text-center navbar">
        <a href="index.php"><button class="btn btn-info btn-md col-lg-12 margin-5"><span class="glyphicon glyphicon-home"></span> <span class="hiding">Home</span></button></a>
        <label class="hiding margin-top"><?= $params['navTitle']; ?></label>

        <?php

            $users = $params['navSuggestions'];
            /* @var $user \model\User */
            foreach ($users as $user) {
                $username = $user->getUsername();
                $userId = $user->getId();
                $userPhoto = $user->getUserPhotoUrl();

                echo "<a href='index.php?page=user&id=$userId'><div class='margin-5 width-100 text-left'><img src='$userPhoto' class='img-circle subImg'><label class='hiding'>$username</label></div></a>";
            }

        ?>

    </div>
