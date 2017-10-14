<?php
require_once 'components/header.php';
require_once 'components/nav.php';

?>

    <div class="col-md-10 justify-content-center text-center">
        <div class="row">
            <div class="col-md-8 thumbnail">
                <video width="600" height="400" controls class="video-style">
                    <source src="../uploads/GOPR0504.MP4" type="video/mp4">
                </video>
                <div class="row">
                    <div class="col-md-4 text-left">
                        <div><h2>Title</h2></div>
                        <div><h4>Description</h4></div>
                        <div><label>Added On: </label>dd-mm-yyyy</div>
                        <div><label>Uploaded by: </label>username</div>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <button class="btn btn-info btn-md col-lg-6"><span class="glyphicon glyphicon-thumbs-up"></span> Like</button>
                        <button class="btn btn-info btn-md col-lg-6"><span class="glyphicon glyphicon-thumbs-down"></span> Dislike</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 well pre-scrollable">

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="../assets/images/thumbnail.jpg">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 1</label>
                        <p>Uploader 1</p>
                    </div>
                </div>

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="../assets/images/thumbnail.jpg">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 2</label>
                        <p>Uploader 2</p>
                    </div>
                </div>

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="../assets/images/thumbnail.jpg">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 3</label>
                        <p>Uploader 3</p>
                    </div>
                </div>

                <div class="well-sm row bg-info">
                    <div class="col-md-8">
                        <img class="thumbnail-scrollbar" src="../assets/images/thumbnail.jpg">
                    </div>
                    <div class="col-md-4 text-left no-padding suggestions-video-text">
                        <label>Video Title 4</label>
                        <p>Uploader 4</p>
                    </div>
                </div>

            </div>
        </div>

        <h2>Comments</h2>
        <div class="form-group row">
            <div class="col-md-10">
                <input type="text" name="commentText" placeholder="Write a comment" class="form-control" maxlength="200">
            </div>
            <div class="col-md-2">
                <button class="btn btn-info btn-md form-control">Comment</button>
            </div>
        </div>
        <div class="well-sm text-left">

            <div class="row bg-info margin-5">
                <div class="col-md-10">
                    <label>Username 1</label>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                        Maecenas porttitor congue massa. Fusce posuere, magna sed pulvinar ultricies,
                        purus lectus malesuada libero, sit amet commodo magna eros quis urna.</p>
                    <p class="date_style">dd-mm-yyyy</p>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info btn-md col-lg-4 margin-comment-buttons"><span class="glyphicon glyphicon-thumbs-up"></span></button>
                    <button class="btn btn-info btn-md col-lg-4 margin-comment-buttons"><span class="glyphicon glyphicon-thumbs-down"></span></button>
                </div>
            </div>

            <div class="row bg-info margin-5">
                <div class="col-md-10">
                    <label>Username 2</label>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                        Maecenas porttitor congue massa. Fusce posuere, magna sed pulvinar ultricies,
                        purus lectus malesuada libero, sit amet commodo magna eros quis urna.
                        Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                        Maecenas porttitor congue massa. Fusce posuere, magna sed pulvinar ultricies,
                        purus lectus malesuada libero, sit amet commodo magna eros quis urna.</p>
                    <p class="date_style">dd-mm-yyyy</p>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info btn-md col-lg-4 margin-comment-buttons"><span class="glyphicon glyphicon-thumbs-up"></span></button>
                    <button class="btn btn-info btn-md col-lg-4 margin-comment-buttons"><span class="glyphicon glyphicon-thumbs-down"></span></button>
                </div>
            </div>

            <div class="row bg-info margin-5">
                <div class="col-md-10">
                    <label>Username 3</label>
                    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit.
                        Maecenas porttitor congue massa. Fusce posuere, magna sed pulvinar ultricies,
                        purus lectus malesuada libero, sit amet commodo magna eros quis urna.</p>
                    <p class="date_style">dd-mm-yyyy</p>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info btn-md col-lg-4 margin-comment-buttons"><span class="glyphicon glyphicon-thumbs-up"></span></button>
                    <button class="btn btn-info btn-md col-lg-4 margin-comment-buttons"><span class="glyphicon glyphicon-thumbs-down"></span></button>
                </div>
            </div>

        </div>

    </div>

<?php
require_once 'components/footer.php';
?>