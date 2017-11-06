<?php

if ($params['page'] == 'profile') {
    /* @var $video \model\Video */
    if (!empty($params['videos'])) {
        foreach ($params['videos'] as $video) {
            $title = $video->getTitle();
            if (strlen($title) >= 45) {
                $title = substr(htmlentities($title), 0, 45);
                $title .= "...";
            }
            else {
                $title = htmlentities($title);
            }
            $thumbnail = $video->getThumbnailURL();
            $videoId = $video->getId();
            echo "
                        <div class='col-md-3 margin-top' id='$videoId' onmouseenter='showVideoButtons(this.id)' onmouseleave='hideVideoButtons(this.id)'>
                            <a href='index.php?controller=video&action=watch&id=$videoId'>
                                <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                <h5 class='text-center text-muted'>$title</h5>
                            </a>
                            <a href='index.php?controller=video&action=edit&id=$videoId'><button class='video-top-btn btn btn-info' id='edit$videoId'>Edit</button></a>
                            <button class='video-middle-btn btn btn-info' id='delete$videoId' onclick='deleteVideo(this.id)'>Delete</button>
                            <button class='video-bottom-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id, \"profile\")'>Add To</button>
                            <div class='video-bottom-div well-sm' id='addToField$videoId'>
                                <p>Choose Playlist:</p>
                                <button class='btn btn-info margin-bottom-5 width-100' id='create$videoId' onclick='createPlaylist(this.id)'>Create New Playlist</button>
                                <div id='videoButtonContainer$videoId'></div>
                            </div>
                        </div>";
        }
    }
}else {
    /* @var $video \model\Video */
    if (!empty($params['videos'])) {
        foreach ($params['videos'] as $video) {
            $title = $video->getTitle();
            if (strlen($title) >= 45) {
                $title = substr(htmlentities($title), 0, 45);
                $title .= "...";
            }
            else {
                $title = htmlentities($title);
            }
            $thumbnail = $video->getThumbnailURL();
            $videoId = $video->getId();
            echo "
                                <div class='col-md-3 margin-top' id='video$videoId' onmouseenter='showAddButton(this.id)' onmouseleave='hideAddButton(this.id)'>
                                    <a href='index.php?controller=video&action=watch&id=$videoId'>
                                        <img src='$thumbnail' class='img-rounded' width='100%' height='auto'>
                                        <h5 class='text-left text-muted'>$title</h5>
                                    </a>
                                    <button class='video-top-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id, \"user\")'>Add To</button>
                                    <div class='video-top-div well-sm' id='addToField$videoId'>
                                        <p>Choose Playlist:</p>
                                        <button class='btn btn-info margin-bottom-5 width-100' id='create$videoId' onclick='createPlaylistFromOther(this.id)'>Create New Playlist</button>
                                        <div id='videoButtonContainer$videoId'></div>
                                    </div>
                                </div>";
        }
    }
}

?>