<div class="col-md-10 justify-content-center text-center">
    <?php
    $resultMsg = $params['result'];
    echo "<h2>$resultMsg</h2>";
    ?>
    <a href='index.php?page=index' class='btn btn-default'>To main page</a>
    <a href='index.php?page=watch&id=<?= $params['video_id']; ?>' class='btn btn-default'>Watch video</a>
</div>
