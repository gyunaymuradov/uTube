<?php
require_once 'components/header.php';
require_once 'components/nav.php';

?>

<div class="col-md-10 justify-content-center text-center">
    <h1>Upload an video</h1>
    <form enctype="multipart/form-data" method="post" action="../controller/uploadVideoController.php">
        <div class="form-group">
        <input type="file" name="videoFile" class="form-control margin-center">
        </div>
        <div class="form-group">
        <input type="text" name="Title" placeholder="Video Title" class="form-control" maxlength="100">
        </div>
        <div class="form-group">
        <input type="text" name="Description" placeholder="Video Description" class="form-control" maxlength="200">
        </div>
        <div class="form-group margin-center">
        <input type="submit" name="Submit" value="Upload Video" class="btn btn-info btn-md col-lg-12">
        </div>
    </form>

</div>

<?php
require_once 'components/footer.php';
?>
