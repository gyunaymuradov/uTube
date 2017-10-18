<div class="col-md-10 justify-content-center text-center">
    <h1>Upload a video</h1>

    <div class="row text-center margin-5" id="preview" style="display: none">
        <h3>Video Preview</h3>
        <video width="400" id="videoPreview" controls></video>
        <h3>Thumbnail Generation</h3>
        <canvas id="canvas" style="display: none"></canvas>
        <div class="form-group">
            <button class="btn btn-info btn-md" onclick="createThumbnail()">Create Thumbnail</button>
        </div>
        <div class="form-group">
            <img id="thumbnailIMG" width="400" height="auto"/>
        </div>

    </div>

    <form enctype="multipart/form-data" method="post" action="index.php?page=upload">
        <input type="hidden" name="Thumbnail" id="thumbnailSRC">
        <div class="form-group">
            <input type="file" name="videoFile" class="form-control margin-center" accept="video/*" onchange="previewVideo(this)" required>
        </div>
        <div class="form-group">
            <input type="text" name="Title" placeholder="Video Title" class="form-control" maxlength="100" required>
        </div>
        <div class="form-group">
            <input type="text" name="Description" placeholder="Video Description" class="form-control" maxlength="200" required>
        </div>
        <div class="form-group">
            <label>Tags: </label>
            <select class="js-example-basic-multiple tags-select" name="Tags[]" multiple="multiple" required>
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
        <input type="submit" name="Submit" value="Upload Video" class="btn btn-info btn-md col-lg-12">
        </div>
    </form>
</div>