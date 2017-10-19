<div class="col-md-10 margin-5">
    <?php

    if (empty($params['result'])) {
        echo "<div class='row margin-5 width-100 text-center'>
                    <h2>Nothing found!</h2>
                    <h3>Sorry, but nothing matched your search criteria. Please try again with different keyword.</h3>
                  </div>";
    } else {
        echo   "<div class='row margin-5 width-100 text-center'>
                    <h2>Search Results:</h2>
                    </div>";
        if ($params['type'] == 'video') {
            $videos = $params['result'];

            foreach ($videos as $video) {
                $thumbnail = $video['thumbnail_url'];
                $id = $video['id'];
                $title = $video['title'];
                $description = $video['description'];

                echo "<div class='row margin-5 width-100 well-sm bg-info'>
                        <div class='col-md-3'>
                            <a href='index.php?page=watch&id=$id'><img src='$thumbnail' width='200' height='auto'></a>
                        </div>
                        <div class='col-md-8'>
                                <a href='index.php?page=watch&id=$id'><h3 class='text-left'>$title</h3></a>
                                <h4>$description</h4>
                        </div>
                    </div>";
            }
        } elseif ($params['type'] == 'user') {
            $users = $params['result'];
            foreach ($users as $user) {
                $id = $user['id'];
                $name = $user['full_name'];
                $photo = $user['user_photo_url'];
                $username = $user['username'];

                echo "<div class='row margin-5 width-100 well-sm bg-info'>
                    <div div class='col-md-3'>
                        <a href='index.php?page=user&id=$id'><img src='$photo' width='200' height='auto'></a>
                    </div>
                    <div class='col-md-8'>
                            <a href='index.php?page=user&id=$id'><h3 class='text-left'>$username</h3></a>
                            <h3>$name</h3>
                    </div>
                  </div>";
            }
        } else {
            $playlists = $params['result'];

            //TODO foreach the playlists and display in divs
        }
    }

    ?>

</div>