<div class="col-md-10 justify-content-center text-center">
    <h1><?= $params['pageTitle'] ?></h1>

    <div class="row text-center margin-5" id="preview" style="display: <?= $params['previewDivDisplay'] ?>">
        <h3>Video Preview</h3>
        <video width="400" src="<?= $params['videoUrl'] ?>" id="videoPreview" controls></video>
        <h3>Thumbnail Generation</h3>
        <canvas id="canvas" style="display:none"></canvas>
        <div class="form-group">
            <button class="btn btn-info btn-md" onclick="createThumbnail()">Create Thumbnail</button>
        </div>
        <div class="form-group">
            <img id="thumbnailIMG" src="<?= $params['thumbnailUrl'] ?>" width="400" height="auto"/>
        </div>

    </div>

    <form enctype="multipart/form-data" method="post" action="index.php?page=upload">
        <input type="hidden" name="Thumbnail" id="thumbnailSRC">
        <div class="form-group">
            <input type="file" name="videoFile" class="form-control margin-center" accept="video/*" onchange="previewVideo(this)" <?= $params['required']; ?> style="display:<?= $params['fileInputDisplay']; ?>">
        </div>
        <div class="form-group">
            <input type="text" name="Title" value="<?= $params['title'] ?>" placeholder="Video Title" class="form-control" maxlength="100" required>
        </div>
        <div class="form-group">
            <input type="text" name="Description" value="<?= $params['description'] ?>" placeholder="Video Description" class="form-control" maxlength="200" required>
        </div>
        <div class="form-group">
            <label>Tags: </label>
            <select class="js-example-basic-multiple tags-select" name="tags" required>
                <?php
                foreach ($params['tags'] as $tag) {
                    $tagName = $tag['name'];
                    $tagId = $tag['id'];
                    echo "<option value='$tagId'>$tagName</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group margin-center">
        <input type="submit" name="Submit" value="<?= $params['btnText'] ?>" class="btn btn-info btn-md col-lg-12">
        </div>
    </form>
</div>