<?php
$mostLiked = $params['most_liked'];
foreach ($mostLiked as $video) {
    $id = $video['id'];
    $title = htmlentities($video['title']);
    $thumbnailUrl = $video['thumbnail_url'];
    echo "<a href='index.php?page=watch&id=$id'>
                        <div class='col-md-3 text-center margin-5'>
                            <img class='img-thumbnail display-block' src='$thumbnailUrl'>
                            <label class='display-block' '>$title</label>
                        </div>
                      </a>";
    }
?>
