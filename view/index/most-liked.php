<?php
$mostLiked = $params['most_liked'];
foreach ($mostLiked as $video) {
    $id = $video['id'];
    if (strlen($video['title']) >= 45) {
    $title = substr(htmlentities($video['title']), 0, 45);
    $title .= "...";
    }
    else {
        $title = htmlentities($video['title']);
    }
    $thumbnailUrl = $video['thumbnail_url'];
    echo "<a href='index.php?controller=video&action=watch&id=$id'>
                        <div class='col-md-3 text-center margin-5'>
                            <img class='img-thumbnail display-block' width='600' height='400' src='$thumbnailUrl'>
                            <label class='display-block' '>$title</label>
                        </div>
                      </a>";
    }
?>
