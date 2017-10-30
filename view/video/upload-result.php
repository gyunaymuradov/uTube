<div class="col-md-10 justify-content-center text-center">
    <?php
    $resultMsg = $params['result'];
    echo "<h2>$resultMsg</h2>";
    ?>
    <a href='index.php?controller=index&action=index' class='btn btn-default'>To main page</a>
    <?php if (!isset($params['success'])) { $id = $params['video_id']; echo "<a href='index.php?controller=video&action=watch&id=$id' class='btn btn-default'>Watch video</a>"; } ?>
</div>
