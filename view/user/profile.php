<div class="col-md-10 no-padding-right margin-center width-100">
    <h3 class="text-left">Your Profile:</h3>
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs nav-justified">
                <li class="active profile-tab-end"><a class="black" data-toggle="tab" href="#about"><h4>About</h4></a></li>
                <li class="profile-tab-middle" onclick="getAboutPage(<?= $params['userId'] ?>, 500)"><a class="black" data-toggle="tab" href="#videos"><h4>Videos</h4></a></li>
                <li class="profile-tab-end" onclick="getAboutPage(<?= $params['userId'] ?>, 500)"><a class="black" data-toggle="tab" href="#playlists"><h4>Playlists</h4></a></li>
            </ul>
            <br>
            <div class="tab-content container-fluid bg-info">
                <div id="about" class="tab-pane fade in active">
                    <div class="row margin-top">
                        <div class="col-md-4 col-md-offset-1">
                            <img src="<?= $params['userPhoto']; ?>" alt="" class="img-rounded profile-img" height="auto">
                        </div>
                        <div class="col-md-4 col-md-offset-1 col-xs-4 col-xs-offset-1">
                            <h3 class="text-muted text-4vw" id="username-old"><?= $params['username']; ?></h3>
                        </div>
                        <div class="col-md-4 col-md-offset-1 col-xs-4">
                            <h3 class="text-muted text-4vw"><?= $params['subscribersCount']; ?> subscribers</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1">
                            <h3 class="text-muted text-4vw">Name: </h3>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <h3 class="text-muted text-4vw"><?= $params['firstName'] . ' ' . $params['lastName']; ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1">
                            <h3 class="text-muted text-4vw">Email: </h3>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <h3 class="text-muted text-4vw"><?= $params['email'] ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1">
                            <h3 class="text-muted text-4vw">Member since: </h3>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <h3 class="text-muted text-4vw"><?= $params['dateJoined']; ?></h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-2 col-xs-4 col-xs-offset-1">
                            <h3 class="text-muted text-4vw">Subscriptions: </h3>
                        </div>
                        <div class="col-md-4 col-xs-6">
                            <h3 class="text-muted text-4vw"><?= $params['subscriptionsCount']; ?></h3>
                        </div>
                    </div>
                    <div class="row text-center margin-bottom-5">
                        <button class="btn btn-info" onclick="getEditForm(<?= $params['userId'] ?>)">Edit profile</button>
                    </div>
                </div>
                <br>
                <div id="videos" class="tab-pane fade row-height">
                    <div class="row" id="videos-container">
                        <?php
                            $videoPagesCount = $params['video_pages_count'];
                            /* @var $video \model\Video */
                            foreach ($params['videos'] as $video) {
                                $title = $video->getTitle();
                                if (strlen($title) >= 20) {
                                    $title = substr(htmlentities($title), 0, 20);
                                    $title .= "...";
                                }
                                else {
                                    $title = htmlentities($title);
                                }
                                $thumbnail = $video->getThumbnailURL();
                                $videoId = $video->getId();
                                echo "
                            <div class=\"col-md-3 margin-top\" id='video$videoId' onmouseenter='showVideoButtons(this.id)' onmouseleave='hideVideoButtons(this.id)'>
                                <a href='index.php?controller=video&action=watch&id=$videoId'>
                                    <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                    <h4 class='text-center text-muted'>$title</h4>
                                </a>
                                <a href='index.php?controller=video&action=edit&id=$videoId'><button class='video-top-btn btn btn-info' id='edit$videoId'>Edit</button></a>
                                <button class='video-middle-btn btn btn-info' id='delete$videoId' onclick='deleteVideo(this.id)'>Delete</button>
                                <button class='video-bottom-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id, \"profile\")'>Add To</button>
                                <div class='video-bottom-div well-sm' id='addToField$videoId'>
                                    <p>Choose Playlist:</p>
                                    <button class='btn btn-info margin-bottom-5 width-100' id='create$videoId' onclick='createPlaylist(this.id)'>Create New Playlist</button>
                                    <div class='pre-scrollable playlist-div' id='videoButtonContainer$videoId'></div>
                                </div>
                            </div>";
                            }
                        ?>
                    </div>
                    <input type="hidden" id="video-pages-count" value="<?= $videoPagesCount; ?>">
                    <input type="hidden" id="current-profile" value="<?= $params['userId']; ?>">
                    <div class="row text-center margin-top" style="display: <?= $params['video_btns_vsblty']; ?>">
                        <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Previous" onclick="previousPage()"><<</button>
                        <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Next" onclick="nextPage()">>></button>
                    </div>
                    <h4></h4>
                </div>
                <div id="playlists" class="tab-pane fade row-height">
                    <div class="row" id="playlists-container">

                        <?php
                            $playlistPagesCount = $params['playlist_pages_count'];
                            /* @var $playlist \model\Playlist */
                            if (!empty($params['playlists'])) {
                                foreach ($params['playlists'] as $playlist) {
                                    $title = $playlist->getTitle();
                                    if (strlen($title) >= 20) {
                                        $title = substr(htmlentities($title), 0, 20);
                                        $title .= "...";
                                    }
                                    else {
                                        $title = htmlentities($title);
                                    }
                                    $thumbnail = $playlist->getThumbnailURL();
                                    $playlistId = $playlist->getId();
                                    echo "
                            <div class=\"col-md-3 margin-top\" id='playlist$playlistId' onmouseenter='showPlaylistButtons(this.id)' onmouseleave='hidePlaylistButtons(this.id)'>
                                <a href='index.php?controller=video&action=watch&playlist-id=$playlistId'>
                                    <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                    <h4 class='text-center text-muted' id='title$playlistId'>$title</h4>
                                </a>
                                <button class='video-top-btn btn btn-info' id='rename$playlistId' onclick='renamePlaylist(this.id)'>Rename</button>
                                <button class='video-middle-btn btn btn-info' id='deletePlaylist$playlistId' onclick='deletePlaylist(this.id)'>Delete Playlist</button>
                                <button class='video-bottom-btn btn btn-info' id='removeVid$playlistId' onclick='showRemoveVid(this.id)'>Remove Video</button>
                                <div class='video-bottom-div well-sm' id='removeField$playlistId' style='display: none'>
                                    <p>Choose Video:</p>
                                    <div class='pre-scrollable playlist-div' id='playlistButtonContainer$playlistId'></div>
                                </div>
                                
                            </div>";
                            }
                         }
                        ?>

                    </div>
                    <input type="hidden" id="playlist-pages-count" value="<?= $playlistPagesCount; ?>">
                    <div class="row text-center margin-top" style="display: <?= $params['playlist_btns_vsblty']; ?>">
                        <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Previous" onclick="previousPagePlaylist()"><<</button>
                        <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Next" onclick="nextPagePlaylists()">>></button>
                    </div>
                    <h4></h4>
                </div>
            </div>
        </div>
    </div>
    <br>
</div>

