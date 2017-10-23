<div class="col-md-10 justify-content-center text-center no-padding-right">
    <div class="well-sm text-left bg-info video-container">
        <h3>Most liked</h3>
        <div class="row text-center">

            <?php

            $mostLiked = $params['mostLiked'];
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
        </div>
    </div>

    <div class="well-sm text-left bg-info video-container">
        <h3>Newest</h3>
        <div class="row text-center">

            <?php

            $newest = $params['newest'];
            foreach ($newest as $video) {
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
        </div>
    </div>
</div>