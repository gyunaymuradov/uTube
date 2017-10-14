<?php
require_once 'components/header.php';
require_once 'components/nav.php';

//TODO: Make right sidebar for suggested videos or playlists, fix comments

//NOTE first div has bg-danger class just to see its borders, it will be removed afterwards
?>

    <div class="col-md-10 justify-content-center text-center bg-danger">
        <video width="600" height="500" controls>
            <source src="" type="video/mp4">
        </video>
        <div class="row">
            <div class="col-md-4 text-left">
            <div><h2>Title</h2></div>
            <div><h4>Description</h4></div>
                <div><label>Added On: </label>dd-mm-yyyy</div>
                <div><label>Uploaded by: </label>username</div>
            </div>
            <div class="col-md-4 col-md-offset-4">
                <button class="btn btn-info btn-md col-lg-4"><span class="glyphicon glyphicon-thumbs-up"></span> Like</button>
                <button class="btn btn-info btn-md col-lg-4"><span class="glyphicon glyphicon-thumbs-down"></span> Dislike</button>
            </div>
        </div>
        <h2>Comments</h2>
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

<?php
require_once 'components/footer.php';
?>