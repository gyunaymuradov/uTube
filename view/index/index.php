<div class="col-md-10 justify-content-center text-center">
    <div class="well-sm text-left bg-info video-container">
        <h3>Most liked</h3>
        <div class="row">

            <?php

            $mostLiked = $params['mostLiked'];
            foreach ($mostLiked as $video) {
                $id = $video['id'];
                $title = $video['title'];
                $thumbnailUrl = $video['thumbnail_url'];
                echo "<a href='index.php?page=watch&id=$id'>
                        <div class='col-md-3 text-center'>
                            <img class='img-thumbnails' src='$thumbnailUrl'>
                            <label>$title</label>
                        </div>
                      </a>";
            }
            ?>
        </div>
    </div>

    <div class="well-sm text-left bg-info video-container">
        <h3>Newest</h3>
        <div class="row">

            <?php

            $newest = $params['newest'];
            foreach ($newest as $video) {
                $id = $video['id'];
                $title = $video['title'];
                $thumbnailUrl = $video['thumbnail_url'];
                echo "<a href='index.php?page=watch&id=$id'>
                        <div class='col-md-3 text-center'>
                            <img class='img-thumbnails' src='$thumbnailUrl'>
                            <label>$title</label>
                        </div>
                      </a>";
            }
            ?>
        </div>
    </div>
</div>