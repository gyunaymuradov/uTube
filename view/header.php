<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="assets/style/library/bootstrap.min.css">
        <script src="assets/js/library/jquery.min.js"></script>
        <script src="assets/js/library/bootstrap.min.js"></script>
        <script src="assets/js/profile.js"></script>
        <link rel="stylesheet" href="assets/style/style.css" type="text/css">
        <title>uTube</title>
    </head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12 bg-info">
               <div class="col-md-2">
                   <a href="index.php"><img src="assets/images/logo.png" height="80" width="auto"></a>
               </div>
                <div class="col-md-6 margin-top">
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-user"></span></button>
                            <button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-facetime-video"></span></button>
                        </div>
                        <input type="text" id="search" class="form-control" placeholder="Search">
                        <span class="input-group-btn">
                            <button class="btn btn-default"><span class="glyphicon glyphicon-search"></span> Search</button>
                        </span>
                    </div>
                </div>
                <div class="col-md-2 col-md-offset-1 margin-top">
                    <a href="index.php?page=upload-form" class="btn btn-info btn-md">Upload video <span class="glyphicon glyphicon-facetime-video"></span></a>
                </div>
                <div class="col-md-1  margin-top">
                    <?php if ($logged) {
                        $userPhotoSrc = $params['userPhotoSrc'];
                        $userId = $params['userId'];
                        echo "<div class=\"dropdown\">
                        <img src=\"$userPhotoSrc\" alt=\"\" width=\"50px\" height=\"auto\" class=\"img-circle dropdown-toggle\"
                                id=\"dropdownMenu1\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                        <ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenu1\">
                            <li class=\"dropdown-item\"><a href=\"index.php?page=profile&id=$userId\">Profile</a></li>
                            <li class=\"dropdown-item\"><a href=\"#\">Channel</a></li>
                            <li class=\"dropdown-item\"><a href=\"index.php?page=logout\">Logout</a></li>
                        </ul>
                    </div>";
                    } else {
                        echo "<a href='index.php?page=login-register' class='btn btn-default'>Sign in</a>";
                    } ?>
                </div>
            </div>
        </div>
