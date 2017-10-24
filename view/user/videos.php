<?php
/* @var $video \model\Video */
foreach ($params['videos'] as $video) {
    $title = $video->getTitle();
    $thumbnail = $video->getThumbnailURL();
    $videoId = $video->getId();
    echo "
                        <div class=\"col-md-3 margin-top\" id='$videoId' onmouseenter='showVideoButtons(this.id)' onmouseleave='hideVideoButtons(this.id)'>
                            <a href='index.php?page=watch&id=$videoId'>
                                <img src=\"$thumbnail\" class=\"img-rounded\" alt=\"\" width=\"100%\" height=\"auto\">
                                <h4 class='text-center text-muted'>$title</h4>
                            </a>
                            <a href='index.php?page=edit-video&id=$videoId'><button class='video-top-btn btn btn-info' id='edit$videoId'>Edit</button></a>
                            <button class='video-middle-btn btn btn-info' id='delete$videoId' onclick='deleteVideo(this.id)'>Delete</button>
                            <button class='video-bottom-btn btn btn-info' id='addToBtn$videoId' onclick='showAddTo(this.id)'>Add To</button>
                            <div class='video-bottom-div well-sm' id='addToField$videoId'>
                                <p>Choose Playlist:</p>
                                <button class='btn btn-info margin-bottom-5 width-100' id='create$videoId' onclick='createPlaylist(this.id)'>Create New Playlist</button>
                                <div id='buttonContainer$videoId'></div>
                            </div>
                        </div>";
}
?>
