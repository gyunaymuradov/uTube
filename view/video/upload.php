<div class="col-md-10 justify-content-center text-center">
    <h2>Upload video</h2>

    <div class="row text-center margin-5" id="preview" style="display:none">
        <h4>Video Preview</h4>
        <video width="400" src="<?= $params['video_url'] ?>" id="videoPreview" controls></video>
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
        <form enctype="multipart/form-data" method="POST" action="index.php?page=upload" id="upload-form" onsubmit="return submitUpload(this)">
            <input type="hidden" name="thumbnail" id="thumbnailSRC">
            <input type="hidden" name="video-id" value="<?= $params['video_id'] ?>">
            <input type="hidden" name="old-thumbnail-url" value="<?= $params['thumbnail_url'] ?>">
            <div class="form-group">
                <input type="file" name="video-file" id="video-file" class="form-control margin-center" accept="video/webm, video/mp4, video/ogg" onchange="validateVideo(); previewVideo(this);" style="display:block">
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
                <input type="text" name="title" id="title" value="<?= htmlentities($params['title']); ?>" onchange="validateTitle()"  placeholder="Video Title" class="form-control">
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
                <textarea name="description" placeholder="Video Description" id="description" onchange="validateDescription()" class="form-control"  rows="4"><?= htmlentities($params['description']); ?></textarea>
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
                <input type="submit" name="Submit" value="Upload" class="btn btn-info btn-md">
            </div>
        </form>
        <script src="assets/js/validations.js"></script>
    </div>
    <br>
</div>