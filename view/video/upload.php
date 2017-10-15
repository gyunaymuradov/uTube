<div class="col-md-10 justify-content-center text-center">
    <h1>Upload a video</h1>
    <form enctype="multipart/form-data" method="post" action="index.php?page=upload.php">
        <div class="form-group">
        <input type="file" name="videoFile" class="form-control margin-center">
        </div>
        <div class="form-group">
        <input type="text" name="Title" placeholder="Video Title" class="form-control" maxlength="100">
        </div>
        <div class="form-group">
        <input type="text" name="Description" placeholder="Video Description" class="form-control" maxlength="200">
        </div>
        <div class="form-group">
            <label>Tags: </label>
            <select class="js-example-basic-multiple tags-select" name="tags" multiple="multiple">
                <option value="tag1">Tag 1</option>
                <option value="tag2">Tag 2</option>
                <option value="tag3">Tag 3</option>
                <option value="tag4">Tag 4</option>
            </select>
        </div>
        <div class="form-group margin-center">
        <input type="submit" name="Submit" value="Upload Video" class="btn btn-info btn-md col-lg-12">
        </div>
    </form>
</div>