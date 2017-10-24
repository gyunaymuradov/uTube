<div class="col-md-10 no-padding-right">
    <h2 class="text-center">Your Profile:</h2>
    <div class="row margin-top">
        <div class="col-md-11 col-md-offset-1">
            <ul class="nav nav-tabs nav-justified">
                <li class="active profile-tab-end"><a class="black" data-toggle="tab" href="#about"><h4>About</h4></a></li>
                <li class="profile-tab-middle" onclick="getAboutPage(<?= $params['userId'] ?>, 500)"><a class="black" data-toggle="tab" href="#videos"><h4>Videos</h4></a></li>
                <li class="profile-tab-end" onclick="getAboutPage(<?= $params['userId'] ?>, 500)"><a class="black" data-toggle="tab" href="#playlists"><h4>Playlists</h4></a></li>

            </ul>
            <div class="tab-content container-fluid bg-info">
                <div id="about" class="tab-pane fade in active">
                    <div class="row margin-top">
                        <div class="col-md-4 col-md-offset-1">
                            <img src="<?= $params['userPhoto']; ?>" alt="" width="100%" class="img-rounded" height="auto">
                        </div>
                        <div class="col-md-4 col-md-offset-2">
                            <h3 class="text-muted" id="username-old"><?= $params['username']; ?></h3>
                        </div>
                        <div class="col-md-4 col-md-offset-2">
                            <h3 class="text-muted"><?= $params['subscribersCount']; ?> <small> subscribers</small></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Name: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?= $params['firstName'] . ' ' . $params['lastName']; ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Email: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?= $params['email'] ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Member since: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?= $params['dateJoined']; ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2">
                            <h3 class="text-muted">Subscriptions: </h3>
                        </div>
                        <div class="col-md-4">
                            <h3 class="text-muted"><?= $params['subscriptionsCount']; ?></h3>
                        </div>
                    </div>
                    <div class="row text-center margin-bottom-5">
                        <button class="btn btn-info" onclick="getEditForm(<?= $params['userId'] ?>)">Edit profile</button>
                    </div>
                </div>
                <div id="videos" class="tab-pane fade">
                    <?php
                    /* @var $video \model\Video */
                    foreach ($params['videos'] as $video) {
                        $title = $video->getTitle();
                        $thumbnail = $video->getThumbnailURL();
                        $videoId = $video->getId();
                        echo "
                        <div class=\"col-md-3 margin-top\" id='video$videoId' onmouseenter='showVideoButtons(this.id)' onmouseleave='hideVideoButtons(this.id)'>
                            <a href='index.php?page=watch&id=$videoId'>
                                <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                <h4 class='text-center text-muted'>$title</h4>
                            </a>
                            <a href='index.php?page=edit-video&id=$videoId'><button class='video-top-btn btn btn-info' id='edit$videoId'>Edit</button></a>
                            <button class='video-middle-btn btn btn-info' id='delete$videoId' onclick='deleteVideo(this.id)'>Delete</button>
                            <button class='video-bottom-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id, \"profile\")'>Add To</button>
                            <div class='video-bottom-div well-sm' id='addToField$videoId'>
                                <p>Choose Playlist:</p>
                                <button class='btn btn-info margin-bottom-5 width-100' id='create$videoId' onclick='createPlaylist(this.id)'>Create New Playlist</button>
                                <div id='videoButtonContainer$videoId'></div>
                            </div>
                        </div>";
                    }
                    ?>
                </div>
                <div id="playlists" class="tab-pane fade">
                    <?php
                    /* @var $playlist \model\Playlist */
                    if ($params['playlists'] != null) {
                        foreach ($params['playlists'] as $playlist) {
                            $title = $playlist->getTitle();
                            $thumbnail = $playlist->getThumbnailURL();
                            $playlistId = $playlist->getId();
                            echo "
                        <div class=\"col-md-3 margin-top\" id='playlist$playlistId' onmouseenter='showPlaylistButtons(this.id)' onmouseleave='hidePlaylistButtons(this.id)'>
                            <a href='index.php?page=watch&playlist-id=$playlistId'>
                                <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                <h4 class='text-center text-muted' id='title$playlistId'>$title</h4>
                            </a>
                            <button class='video-top-btn btn btn-info' id='rename$playlistId' onclick='renamePlaylist(this.id)'>Rename</button>
                            <button class='video-middle-btn btn btn-info' id='removeVid$playlistId' onclick='showRemoveVid(this.id)'>Remove Video</button>
                            <div class='video-middle-div well-sm' id='removeField$playlistId' style='display: none'>
                                <p>Choose Video:</p>
                                <div id='playlistButtonContainer$playlistId'></div>
                            </div>
                            
                        </div>";
                        }
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
    <br>
</div>

<!-- onclick="getAboutPage(<?= $params['userId'] ?>)"