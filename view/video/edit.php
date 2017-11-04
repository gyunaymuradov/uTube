<div class="col-md-10 justify-content-center text-center no-padding-right margin-center">
    <h2>Edit video</h2>

    <div class="row text-center margin-5" id="preview" style="display:block">
        <h4>Video Preview</h4>
        <video id="videoPlayer" class="video-js vjs-big-play-centered" controls preload="auto" width="400" height="260"  data-setup='{"aspectRatio":"600:400", "fluid": true, "playbackRates": [0.5, 1, 1.5, 2] }' style="margin-left: auto; margin-right: auto">
            <source src="<?= $params['video_url']; ?>" type='video/mp4'>
            <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that
                <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
            </p>
        </video>
        <h4>Thumbnail Generation</h4>
        <canvas id="canvas" style="display:none"></canvas>
        <div class="form-group">
            <button class="btn btn-info btn-md" onclick="createThumbnail()">Create Thumbnail</button>
        </div>
        <div class="form-group">
            <img id="thumbnailIMG" src="<?= $params['thumbnail_url'] ?>" width="400" height="auto"/>
        </div>

    </div>
    <div class="col-md-8 col-md-offset-2 text-center">
        <form enctype="multipart/form-data" method="POST" action="index.php?controller=video&action=edit" id="edit-video-form" onsubmit="return submitEditVideo(this)">
            <input type="hidden" name="thumbnail" id="thumbnailSRC">
            <input type="hidden" name="video-id" value="<?= $params['video_id'] ?>">
            <input type="hidden" name="old-thumbnail-url" value="<?= $params['thumbnail_url'] ?>">
            <div class="form-group">
                <input type="file" name="video-file" id="video-file" class="form-control margin-center" accept="video/webm, video/mp4, video/ogg" onchange="previewVideo(this);" style="display:none">
                <div id="video-file-errors">
                    <?php
                    if (!empty($params['errors']['video'])) {
                        foreach ($params['errors']['video'] as $error) {
                            echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <input type="text" name="title" id="title" value="<?= htmlentities($params['title']); ?>" onblur="validateTitle()" onmousemove="validateTitle()" onmouseover="validateTitle()" onmouseout="validateTitle()"  placeholder="Video Title" class="form-control">
                <div id="title-errors">
                    <?php
                    if (!empty($params['errors']['title'])) {
                        foreach ($params['errors']['title'] as $error) {
                            echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <textarea name="description" placeholder="Video Description" id="description" onblur="validateDescription()" onmousemove="validateDescription()" onmouseover="validateDescription()" onmouseout="validateDescription()" class="form-control"  rows="4"><?= htmlentities($params['description']); ?></textarea>
                <div id="description-errors">
                    <?php
                    if (!empty($params['errors']['description'])) {
                        foreach ($params['errors']['description'] as $error) {
                            echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="form-group col-md-4 col-md-offset-4">
                <label>Category: </label>
                <select class="form-control" name="tags">
                    <?php
                    foreach ($params['tags'] as $tag) {
                        $tagName = $tag['name'];
                        $tagId = $tag['id'];
                        if ($params['tag'] == $tagId) {
                            echo "<option value=$tagId selected>$tagName</option>";
                        } else {
                            echo "<option value=$tagId>$tagName</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="form-group margin-center col-md-12">
                <input type="submit" name="Submit" value="Edit" class="btn btn-info btn-md">
            </div>
            <div class="col-md-4 margin-top"></div>
        </form>
        <script src="assets/js/validations.js"></script>
    </div>
</div>