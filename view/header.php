<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="assets/images/favicon.ico">
        <link rel="stylesheet" href="assets/style/library/bootstrap.min.css">
        <script src="assets/js/library/jquery.min.js"></script>
        <script src="assets/js/library/bootstrap.min.js"></script>
        <script src="assets/js/profile.js"></script>
        <script src="assets/js/nav.js"></script>
        <script src="assets/js/index.js"></script>
        <script src="assets/js/video.js"></script>
        <link rel="stylesheet" href="assets/style/style.css" type="text/css">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
        <script src="assets/js/video_preview.js"></script>
        <title>uTube</title>
    </head>
<body onload="clickListener()">
    <button class="btn btn-info btn-md sidenav-btn" onclick="toggleSidebar()"><span class="glyphicon glyphicon-menu-hamburger"></span></button>
    <div class="container">
        <div class="row bg-info">

               <div class="col-md-2 col-xs-2 logo-container">
                   <a href="index.php"><img src="assets/images/logo.png" class="logo"></a>
               </div>

                <div class="col-md-6 margin-top hiding">
                    <form action="index.php?page=search" method="post">
                        <div class="input-group ">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-primary" onclick="searchOption('playlist')"><span class="glyphicon glyphicon-play-circle"></span></button>
                                <button type="button" class="btn btn-primary" onclick="searchOption('user')"><span class="glyphicon glyphicon-user"></span></button>
                                <button type="button" class="btn btn-primary" onclick="searchOption('video')"><span class="glyphicon glyphicon-facetime-video"></span></button>
                            </div>
                            <input type="text" name="value" id="search" class="form-control autocomplete-item" onkeyup="getSuggestions()" placeholder="<?= $params['searchPlaceholder']; ?>" autocomplete="off">
                            <div id="search-autocomplete"></div>
                            <input type="hidden" name="search-option" id="search-for" value="video">
                            <span class="input-group-btn">
                                <button class="btn btn-default" name="search"><span class="glyphicon glyphicon-search"></span> Search</button>
                            </span>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 col-xs-5 margin-top">
                <?php if($logged) {
                    echo "<a href='index.php?page=upload' class='btn btn-info btn-md'>Upload Video <span class='glyphicon glyphicon-facetime-video'></span></a>";
                } ?>
                </div>
                <div class="col-md-1 col-xs-4 margin-top">
                    <?php if ($logged) {
                        $userPhotoSrc = $params['userPhotoSrc'];
                        $userId = $params['userId'];
                        echo "<div class='dropdown'>
                        <img src='$userPhotoSrc' width='50px' height='auto' class='img-rounded dropdown-toggle cursor-pointer avatar'
                                id='dropdownMenu1' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                        <ul class='dropdown-menu' aria-labelledby=\"dropdownMenu1\">
                            <li class='dropdown-item'><a href='index.php?page=profile&id=$userId'>Profile</a></li>
                            <li class='dropdown-item'><a href='index.php?page=logout'>Logout</a></li>
                        </ul>
                    </div>";
                    } else {
                        echo "<a href='index.php?page=login' class='btn btn-default'>Sign in</a>";
                    } ?>
                </div>
        </div>
<!--        <div class="row bg-info showing">-->
<!--            <div class="col-xs-12">-->
<!--                <div class="input-group ">-->
<!--                    <form action="index.php?page=search" method="post">-->
<!--                        <div class="input-group-btn">-->
<!--                            <button type="button" class="btn btn-primary" onclick="searchOption('user')"><span class="glyphicon glyphicon-user"></span></button>-->
<!--                            <button type="button" class="btn btn-primary" onclick="searchOption('video')"><span class="glyphicon glyphicon-facetime-video"></span></button>-->
<!--                        </div>-->
<!--                        <input type="text" id="search" name="search" class="form-control" placeholder="Search">-->
<!--                        <input type="hidden" id="search-for" value="video">-->
<!--                        <span class="input-group-btn">-->
<!--                                <button class="btn btn-default"><span class="glyphicon glyphicon-search"></span> Search</button>-->
<!--                        </span>-->
<!--                    </form>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
