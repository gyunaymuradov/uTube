<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Getting started</title>
        <link rel="stylesheet" href="assets/style/getting_started.css" type="text/css">
        <link rel="stylesheet" href="assets/style/library/bootstrap.min.css">
        <link rel="icon" href="assets/images/favicon.ico">
        <script src="assets/js/library/jquery.min.js"></script>
        <script src="assets/js/library/bootstrap.min.js"></script>
        <script src="assets/js/getting_started_js.js"></script>

    </head>
    <body>
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="jumbotron" id="jumbo">
                    <h3 class="text-center">Please sign in or sign up to get all the benefits from the website</h3>
                    <div class="text-center">
                        <img src="assets/images/logo.png" width="130" height="auto">
                    </div>
                </div>
                <div class="panel panel-login">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-6">
                                <a href="#" class="active" id="login-form-link">Sign in</a>
                            </div>
                            <div class="col-xs-6">
                                <a href="#" id="register-form-link">Sign up</a>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="login-form" action="index.php?page=login" method="post" role="form" style="display: block;">
                                    <div class="form-group">
                                        <input type="text" name="username" tabindex="1" class="form-control" placeholder="Username">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" tabindex="2" class="form-control" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <input type="submit" name="login" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form id="register-form" action="index.php?page=register" method="post" role="form" style="display: none;" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <input type="text" name="username" tabindex="1" class="form-control" placeholder="Username" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" tabindex="1" class="form-control" placeholder="Email Address" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="firstName" tabindex="1" class="form-control" placeholder="First Name" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="lastName" tabindex="1" class="form-control" placeholder="Last Name" value="">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" tabindex="2" class="form-control" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="confirmPass" tabindex="2" class="form-control" placeholder="Confirm Password">
                                    </div>
                                    <div class="form-group">
                                        <label for="photo" class="btn btn-link col-sm-12 form-control" tabindex="2">Upload Profile Photo</label>
                                        <input type="file" id="photo" name="photo" style="visibility:hidden;">
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <input type="submit" name="register"  tabindex="4" class="form-control btn btn-register" value="Register">
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
    </body>
</html>