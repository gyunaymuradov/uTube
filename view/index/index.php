<div class="col-md-10 justify-content-center text-center no-padding-right ">
    <div class="well-sm text-left bg-info video-container">
        <h3 class="remove-margin-top">Most liked</h3>
        <div class="row text-center row-height" id="most-liked">

            <?php
            $mostLikedPagesCount = $params['most_liked_pages_count'];
            $mostLiked = $params['most_liked'];
            foreach ($mostLiked as $video) {
                $id = $video['id'];
                $title = htmlentities($video['title']);
                $thumbnailUrl = $video['thumbnail_url'];
                echo "<a href='index.php?controller=video&action=watch&id=$id'>
                        <div class='col-md-3 text-center margin-5'>
                            <img class='img-thumbnail display-block' width='600' height='400' src='$thumbnailUrl'>
                            <label class='display-block' '>$title</label>
                        </div>
                      </a>";
            }
            ?>
        </div>
        <input type="hidden" id="liked-pages-count" value="<?= $mostLikedPagesCount; ?>">
        <div class="row text-center margin-top">
            <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Previous Videos" onclick="previousMostLiked()"><<</button>
            <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Next Videos" onclick="nextMostLiked()">>></button>
        </div>
        <h5></h5>
    </div>

    <div class="well-sm text-left bg-info video-container">
        <h3 class="remove-margin-top">Newest</h3>
        <div class="row text-center row row-height" id="newest">

            <?php
            $newestPagesCount = $params['newest_pages_count'];
            $newest = $params['newest'];
            foreach ($newest as $video) {
                $id = $video['id'];
                $title = htmlentities($video['title']);
                $thumbnailUrl = $video['thumbnail_url'];
                echo "<a href='index.php?controller=video&action=watch&id=$id'>
                        <div class='col-md-3 text-center margin-5'>
                            <img class='img-thumbnail display-block' width='600' height='400' src='$thumbnailUrl'>
                            <label class='display-block' '>$title</label>
                        </div>
                      </a>";
            }
            ?>
        </div>
        <div class="row text-center margin-top">
            <input type="hidden" id="newest-pages-count" value="<?= $newestPagesCount; ?>">
            <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Previous Videos" onclick="previousNewest()"><<</button>
            <button class="btn btn-group btn-lg btn-info" data-toggle="tooltip" title="Next Videos" onclick="nextNewest()">>></button>
        </div>
        <h5></h5>
    </div>
</div>