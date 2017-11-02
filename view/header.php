<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="assets/images/favicon.ico">
        <link rel="stylesheet" href="assets/style/library/bootstrap.min.css">
        <script src="assets/js/library/jquery.min.js"></script>
        <script src="assets/js/library/bootstrap.min.js"></script>
        <script src="assets/js/validations.js"></script>
        <script src="assets/js/profile.js"></script>
        <script src="assets/js/responsive.js"></script>
        <script src="assets/js/index.js"></script>
        <script src="assets/js/video.js"></script>
        <link rel="stylesheet" href="assets/style/style.css" type="text/css">
        <script src="assets/js/tooltip.js"></script>

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.4/js/select2.min.js"></script>
        <script src="assets/js/video_preview.js"></script>

<!--        For video.js player-->
        <link href="http://vjs.zencdn.net/6.2.8/video-js.css" rel="stylesheet">
        <script src="http://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
        <link rel="stylesheet" href="assets/style/player-skin.css">

        <title>uTube</title>
    </head>
<body onload="clickListener(); respondToSize();">
<div id="fb-root"></div>
<script>
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = 'https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=1340131456099155';
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

    <button class="btn btn-info btn-md sidenav-btn" onclick="toggleSidebar()"><span class="glyphicon glyphicon-menu-hamburger"></span></button>
    <div class="container">
        <div class="row bg-info">

               <div class="col-md-2 col-xs-2 logo-container">
                   <a href="index.php"><img src="assets/images/logo.png" class="logo"></a>
               </div>
               <div id="searchBarContainerLarge">
                    <div class="col-md-5 margin-top margin-bottom-5" id="searchBar">
                        <form action="index.php?controller=index&action=search" method="post">
                            <div class="input-group ">
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Search playlists" onclick="searchOption('playlist')"><span class="glyphicon glyphicon-play-circle"></span></button>
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Search users" onclick="searchOption('user')"><span class="glyphicon glyphicon-user"></span></button>
                                    <button type="button" class="btn btn-info" data-toggle="tooltip" title="Search videos" onclick="searchOption('video')"><span class="glyphicon glyphicon-facetime-video"></span></button>
                                </div>
                                <input type="text" name="value" id="search" class="form-control autocomplete-item" onkeyup="getSuggestions()" placeholder="<?= $params['search_placeholder']; ?>" autocomplete="off">
                                <div id="search-autocomplete"></div>
                                <input type="hidden" name="search-option" id="search-for" value="video">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" name="search"><span class="glyphicon glyphicon-search"></span> Search</button>
                                </span>
                            </div>
                        </form>
                    </div>
               </div>
                <div class="col-md-2 col-xs-4 margin-top">
                    <?php if (isset($_SESSION['user'])) {
                        echo " <a href='index.php?controller=video&action=upload' class='btn btn-info btn-md'>Upload <span class='hiding'>Video </span><span class='glyphicon glyphicon-facetime-video'></span></a>";
                    } ?>
                </div>
                <div class="col-md-2 col-xs-5 margin-top no-padding-right">
                    <?php if ($logged) {
                        $userPhotoSrc = $params['user_photo_src'];
                        $firstName = $params['first_name'];
                        $userId = $params['user_id'];
                        echo "<div class='dropdown' data-toggle='tooltip' title='Your Profile'>
                            <img src='$userPhotoSrc' width='45px' height='auto' class='img-rounded dropdown-toggle'
                                id='dropdownMenu1' aria-haspopup='true' aria-expanded='false'>&nbsp;<span data-toggle='dropdown' class='cursor-pointer avatar'><small  id='first-name-header'>Hello, $firstName!</small></span>
                        <ul class='dropdown-menu' aria-labelledby='dropdownMenu1'>
                            <li class='dropdown-item'><a href='index.php?controller=user&action=profile&id=$userId'>Profile</a></li>
                            <li class='dropdown-item'><a href='index.php?controller=user&action=logout'>Logout</a></li>
                        </ul>
                        
                    </div>";
                    } else {
                        echo "<a href='index.php?controller=user&action=login' class='btn btn-default'>Sign in</a>";
                    } ?>
                </div>
        </div>
        <div class="row bg-info" id="searchBarContainerSmall">
<!--            this is for the responsive design-->
        </div>

