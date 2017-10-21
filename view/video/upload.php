<div class="col-md-10 justify-content-center text-center">
    <h2><?= $params['page_title'] ?></h2>

    <div class="row text-center margin-5" id="preview" style="display: <?= $params['preview_div_display'] ?>">
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
        <form enctype="multipart/form-data" method="post" action="<?= $params['form_action'] ?>">
            <input type="hidden" name="thumbnail" id="thumbnailSRC">
            <input type="hidden" name="video-id" value="<?= $params['video_id'] ?>">
            <input type="hidden" name="old-thumbnail-url" value="<?= $params['thumbnail_url'] ?>">
            <div class="form-group">
                <input type="file" name="video-file" class="form-control margin-center" accept="video/*" onchange="previewVideo(this)" <?= $params['required']; ?> style="display:<?= $params['file_input_display']; ?>">
            </div>
            <div class="form-group">
                <input type="text" name="title" value="<?= htmlentities($params['title']); ?>" placeholder="Video Title" class="form-control" maxlength="100" required>
            </div>
            <div class="form-group">
                <textarea name="description" placeholder="Video Description" class="form-control" maxlength="200" rows="4" required><?= htmlentities($params['description']); ?></textarea>
            </div>
            <div class="form-group col-md-4 col-md-offset-4">
                <label>Tags: </label>
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
                <input type="submit" name="Submit" value="<?= $params['btn_text'] ?>" class="btn btn-info btn-md">
            </div>
        </form>
    </div>
</div>