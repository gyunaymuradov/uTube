<div class="col-md-10 no-padding-right">
    <h3 class="text-left"><?= $params['username']; ?>'s Profile:</h3>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs nav-justified">
                <li class="active profile-tab-end"><a class="black" data-toggle="tab" href="#about"><h4>About</h4></a></li>
                <li class="profile-tab-middle"><a class="black" data-toggle="tab" href="#videos"><h4>Videos</h4></a></li>
                <li class="profile-tab-end"><a class="black" data-toggle="tab" href="#playlists"><h4>Playlists</h4></a></li>
            </ul>
            <br>
            <div class="tab-content container-fluid bg-info">
                <div id="about" class="tab-pane fade in active">
                    <div class="row">
                        <div class="col-md-4 col-md-offset-1"><br>
                            <img src="<?= $params['userPhoto']; ?>" alt="" width="100%" class="img-rounded" height="auto">
                        </div>
                        <div class="col-md-4 col-md-offset-1">
                            <h3 class="text-muted"><?= $params['username']; ?></h3>
                        </div>
                        <div class="col-md-4 col-md-offset-1">
                            <h3 class="text-muted""><span id="subscribers"><?= $params['subscribersCount']; ?></span> <small> subscribers</small></h3>
                            <button class="btn btn-info" onclick="subscribe(<?= $params['profileId']; ?>)" id="subscribe"><?= $params['subscribeButton']; ?></button>
                            <input type="hidden" id="logged" value="<?= $params['logged']; ?>">
                            <input type="hidden" id="current-profile" value="<?= $params['profileId']; ?>">
                            <input type="hidden" id="loggedUserId" value="<?= $params['loggedUserId']; ?>">
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
                    <br>
                </div>
                <div id="videos" class="tab-pane fade">
                    <div class="row" id="videos-container">
                        <?php
                            $videoPagesCount = $params['video_pages_count'];
                            /* @var $video \model\Video */
                            foreach ($params['videos'] as $video) {
                                $title = $video->getTitle();
                                $thumbnail = $video->getThumbnailURL();
                                $videoId = $video->getId();
                                echo "
                                <div class='col-md-3 margin-top' id='video$videoId' onmouseenter='showAddButton(this.id)' onmouseleave='hideAddButton(this.id)'>
                                    <a href='index.php?controller=video&action=watch&id=$videoId'>
                                        <img src='$thumbnail' class='img-rounded' width='100%' height='auto'>
                                        <h4 class='text-left text-muted'>$title</h4>
                                    </a>
                                    <button class='video-top-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id, \"user\")'>Add To</button>
                                    <div class='video-top-div well-sm' id='addToField$videoId'>
                                        <p>Choose Playlist:</p>
                                        <button class='btn btn-info margin-bottom-5 width-100' id='create$videoId' onclick='createPlaylistFromOther(this.id)'>Create New Playlist</button>
                                        <div id='videoButtonContainer$videoId'></div>
                                    </div>
                                </div>";
                            }
                        ?>
                    </div>
                    <input type="hidden" id="video-pages-count" value="<?= $videoPagesCount; ?>">
                    <div class="row text-center margin-top" style="display: <?= $params['video_btns_vsblty']; ?>">
                        <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Previous" onclick="previousPage('user')"><<</button>
                        <button class="btn btn-group btn-lg btn-info"  data-toggle="tooltip" title="Next" onclick="nextPage('user')">>></button>
                    </div>
                    <h4></h4>
                </div>
                <div id="playlists" class="tab-pane fade">
                    <div class="row profile-row-height" id="playlists-container">
                        <?php
                        $playlistPagesCount = $params['playlist_pages_count'];
                        /* @var $playlist \model\Playlist */
                        if (!empty($params['playlists'])) {
                            foreach ($params['playlists'] as $playlist) {
                                $title = $playlist->getTitle();
                                $thumbnail = $playlist->getThumbnailURL();
                                $playlistId = $playlist->getId();
                                echo "
                            <div class=\"col-md-3 margin-top\" id='playlist$playlistId'>
                                <a href='index.php?controller=video&action=watch&playlist-id=$playlistId'>
                                    <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                    <h4 class='text-center text-muted'>$title</h4>
                                </a>
                            </div>";
                            }
                        }
                        ?>
                    </div>
                <input type="hidden" id="playlist-pages-count" value="<?= $playlistPagesCount; ?>">
                <div class="row text-center margin-top" style="display: <?= $params['playlist_btns_vsblty']; ?>">
                    <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Previous" onclick="previousPagePlaylist('user')"><<</button>
                    <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Next" onclick="nextPagePlaylists('user')">>></button>
                </div>
                <h4></h4>
            </div>
        </div>
    </div>
    </div>
    <br>
</div>

