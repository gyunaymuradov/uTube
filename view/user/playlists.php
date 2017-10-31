<?php

if ($params['page'] == 'profile') {
    /* @var $playlist \model\Playlist */
    if (!empty($params['playlists'])) {
        foreach ($params['playlists'] as $playlist) {
            $title = $playlist->getTitle();
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
                                        <div id='playlistButtonContainer$playlistId'></div>
                                    </div>
    
                                </div>";
        }
    }
} else {
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
}
?>