<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign up</title>
    <script src="assets/js/library/jquery.min.js"></script>
    <script src="assets/js/library/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/style/getting_started.css" type="text/css">
    <link rel="stylesheet" href="assets/style/library/bootstrap.min.css">
    <link rel="icon" href="assets/images/favicon.ico">

</head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="jumbotron" id="jumbo">
                        <h3 class="text-center">Please sign up to get all the benefits from the website</h3>
                        <div class="text-center">
                            <a href="index.php"><img src="assets/images/logo.png" width="130" height="auto"></a>
                        </div>
                    </div>
                    <div class="panel panel-login">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-12">
                                    <label class="active" id="register-form-link">Sign up</label>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="register-form" action="index.php?page=register" method="post" role="form" enctype="multipart/form-data" onsubmit="submitRegister(e)">
                                        <div class="form-group">
                                            <input type="text" name="username" tabindex="1" id="username" onblur="validateUsername()" class="form-control" placeholder="Username" value="<?= htmlentities($params['username']); ?>" maxlength="15">
                                            <div id="username-errors">
                                                <?php
                                                if (!empty($params['errors']['username'])) {
                                                foreach ($params['errors']['username'] as $error) {
                                                    echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" tabindex="1" id="email" onblur="validateEmail()" class="form-control" placeholder="Email Address" value="<?= htmlentities($params['email']); ?>" >
                                        <div id="email-errors">
                                            <?php
                                                if (!empty($params['errors']['email'])) {
                                                    foreach ($params['errors']['email'] as $error) {
                                                        echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                    }
                                                }
                                                ?>
                                        </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="first-name" tabindex="1" id="first-name" onblur="validateFirstName()" class="form-control" placeholder="First Name" value="<?= htmlentities($params['first-name']); ?>" maxlength="15" >
                                            <div id="first-name-errors">
                                                <?php
                                                if (!empty($params['errors']['first_name'])) {
                                                    foreach ($params['errors']['first_name'] as $error) {
                                                        echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="last-name" tabindex="1" id="last-name" class="form-control" onblur="validateLastName()" placeholder="Last Name" value="<?= htmlentities($params['last-name']); ?>" maxlength="15" >
                                            <div id="last-name-errors">
                                                <?php
                                                if (!empty($params['errors']['last_name'])) {
                                                    foreach ($params['errors']['last_name'] as $error) {
                                                        echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" tabindex="2" id="password" onblur="validatePassword()" class="form-control" maxlength="20" placeholder="Password" >
                                            <div id="password-errors">
                                                <?php
                                                if (!empty($params['errors']['password'])) {
                                                    foreach ($params['errors']['password'] as $error) {
                                                        echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="confirm-pass" id="confirm-password" onblur="validatePassword()" tabindex="2" class="form-control" maxlength="20" placeholder="Confirm Password">
                                            <div id="confirm-password-errors">
                                                    <?php
                                                    if (!empty($params['errors']['confirm-pass'])) {
                                                        $error = $params['errors']['confirm-pass'];
                                                            echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                    }
                                                    ?>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="photo" class="btn btn-register col-sm-12 form-control" tabindex="2">Upload Profile Photo</label>
                                            <input type="file" id="photo" name="photo" onchange="validateImage()" style="visibility:hidden;" accept="image/x-png,image/jpg,image/jpeg">
                                            <div id="file-error"></div>
                                            <?php
                                            if (!empty($params['errors']['img'])) {
                                                foreach ($params['errors']['img'] as $error) {
                                                    echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="register" tabindex="4" class="form-control btn btn-register" value="Register">
                                                </div>
                                                <div class="col-sm-6 col-sm-offset-3"><br>
                                                    <a href="index.php?page=login" class="btn form-control btn-register">Sign in</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">&copy; 2017 uTube</div>
                    </div>
                </div>
            </div>
        </div>
        <script src="assets/js/validations.js"></script>
    </body>
</html>