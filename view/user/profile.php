<div class="col-md-10">
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <img src="<?= $params['userPhoto']; ?>" alt="" width="250" class="img-rounded" height="auto">
        </div>
        <div class="col-md-4 col-md-offset-2">
            <h3 class="text-muted"><?= $params['username']; ?></h3>
        </div>
        <div class="col-md-4 col-md-offset-2">
            <h3 class="text-muted"><?= $params['subscribersCount']; ?> <small> subscribers</small></h3>
        </div>
    </div>
    <div class="row margin-top">
        <div class="col-md-11 col-md-offset-1">
            <ul class="nav nav-tabs nav-justified">
                <li class="active profile-tab-end"><a data-toggle="tab" href="#videos"><h4>Videos</h4></a></li>
                <li><a class="profile-tab-middle" data-toggle="tab" href="#playlists"><h4>Playlists</h4></a></li>
                <li><a class="profile-tab-end" data-toggle="tab" href="#about"><h4>About</h4></a></li>
            </ul>
            <div class="tab-content container-fluid bg-info">
                <div id="videos" class="tab-pane fade in active">
                    <?php
                    /* @var $video \model\Video */
                        foreach ($params['videos'] as $video) {
                            $title = $video->getTitle();
                            $thumbnail = $video->getThumbnailURL();
                            $videoId = $video->getId();
                            echo "
                        <div class=\"col-md-3 margin-top\" id='$videoId' onmouseenter='showButtons(this.id)' onmouseleave='hideButtons(this.id)'>
                            <a href='index.php?page=watch&id=$videoId'>
                                <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"200\" height=\"auto\">
                                <h4 class='text-center text-muted'>$title</h4>
                            </a>
                            <button class='video-edit-btn btn btn-info' id='edit$videoId'>Edit</button>
                            <button class='video-delete-btn btn btn-info' id='delete$videoId' onclick='deleteVideo(this.id)'>Delete</button>
                            <button class='video-addTo-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id)'>Add To</button>
                            <div class='video-addTo-div well-sm' id='addToField$videoId'>
                                <p>Choose Playlist:</p>
                                <button class='btn btn-info margin-bottom-5' id='create$videoId' onclick='createPlaylist(this.id)'>Create New Playlist</button>
                                <div id='buttonContainer$videoId'></div>
                            </div>
                        </div>";
                        }
                    ?>
                </div>
                <div id="playlists" class="tab-pane fade">
                    <?php
                    /* @var $playlist \model\Playlist */
                    foreach ($params['playlists'] as $playlist) {
                        $title = $playlist->getTitle();
                        $thumbnail = $playlist->getThumbnailURL();
                        $playlistId = $playlist->getId();
                        echo "
                        <div class=\"col-md-3 margin-top\" id='$playlistId' onmouseenter='showButtons(this.id)' onmouseleave='hideButtons(this.id)'>
                            <a href='index.php?page=watch&id=$videoId'>
                                <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"200\" height=\"auto\">
                                <h4 class='text-center text-muted'>$title</h4>
                            </a>
                        </div>";
                    }
                    ?>
                </div>
                <div id="about" class="tab-pane fade">
                    <div class="col-md-3 col-md-offset-2">
                        <h3 class="text-muted">Name: </h3>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-muted"><?= $params['firstName'] . ' ' . $params['lastName']; ?></h3>
                    </div>
                    <div class="col-md-1 margin-top">
                        <button class="btn btn-info" onclick="getEditForm(<?= $params['userId'] ?>)">Edit profile</button>
                    </div>
                    <div class="col-md-3 col-md-offset-2">
                        <h3 class="text-muted">Email: </h3>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-muted"><?= $params['email'] ?></h3>
                    </div>
                    <div class="col-md-3 col-md-offset-2">
                        <h3 class="text-muted">Member since: </h3>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-muted"><?= $params['dateJoined']; ?></h3>
                    </div>
                    <div class="col-md-3 col-md-offset-2">
                        <h3 class="text-muted">Subscriptions: </h3>
                    </div>
                    <div class="col-md-4">
                        <h3 class="text-muted"><?= $params['subscriptionsCount']; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
</div>

