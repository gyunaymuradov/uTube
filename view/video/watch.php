

<!--TODO: Make right sidebar for suggested videos or playlists, fix comments

//NOTE first div has bg-danger class just to see its borders, it will be removed afterwards -->

<div class="col-md-10 justify-content-center bg-danger margin-top">
    <video width="500" height="auto" controls>
        <source src="<?= $params['videoUrl']; ?>" type="video/mp4">
    </video>
    <div class="row">
        <div class="col-md-5 text-left">
        <div><h3><?= $params['videoTitle']; ?></h3></div>
        <div><h5><?= $params['videoDescription']; ?></h5></div>
            <div><label>Added On: <?= $params['dateAdded']; ?></label></div>
            <div><label>Uploaded by: <?= $params['uploader']; ?></label></div>
        </div>
        <div class="col-md-4  margin-top">
            <button class="btn btn-info btn-md col-lg-4"><span class="glyphicon glyphicon-thumbs-up"></span> Like</button>
            <button class="btn btn-info btn-md col-lg-4"><span class="glyphicon glyphicon-thumbs-down"></span> Dislike</button>
        </div>
    </div>
    <h3>Comments</h3>
    <ul>
        <li>
            <div>
                <label><strong>User Commented</strong></label>
<p>Comment Text</p>
<p class="date_style">dd-mm-yyyy</p>
</div>
</li>

<li>
    <div>
        <label><strong>User Commented</strong></label>
        <p>Comment Text</p>
        <p class="date_style">dd-mm-yyyy</p>
    </div>
</li>
</ul>
</div>