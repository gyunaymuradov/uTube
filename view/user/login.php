<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Sign in</title>
        <link rel="stylesheet" href="assets/style/getting_started.css" type="text/css">
        <link rel="stylesheet" href="assets/style/library/bootstrap.min.css">
        <link rel="icon" href="assets/images/favicon.ico">
        <script src="assets/js/library/jquery.min.js"></script>
        <script src="assets/js/library/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="jumbotron" id="jumbo">
                        <h3 class="text-center">Sign in to get all the benefits from the website</h3>
                        <div class="text-center">
                            <a href="index.php"><img src="assets/images/logo.png" width="130" height="auto"></a>
                        </div>
                    </div>
                    <div class="panel-login">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-xs-12">
                                    <label class="active" id="login-form-link">Sign in</label>
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="panel-body">
                            <h4 class="text-danger text-center"><?php echo $params['errors']; ?></h4>
                            <div class="row">
                                <div class="col-lg-12">
                                    <form id="login-form" action="index.php?controller=user&action=login" method="post" role="form">
                                        <div class="form-group">
                                            <input type="text" name="username" tabindex="1" class="form-control" value="<?= htmlentities($params['username']); ?>" placeholder="Username" required autocomplete="off">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" tabindex="2" class="form-control" placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-sm-6 col-sm-offset-3">
                                                    <input type="submit" name="login" id="login-submit" tabindex="4" class="form-control btn btn-login" value="Log In">
                                                </div>
                                                <div class="col-sm-6 col-sm-offset-3"><br>
                                                    <a href="index.php?controller=user&action=register" class="btn form-control btn-register">Sign up</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="text-center">&copy; 2017 <strong>uTube</strong> | By <strong>Gyunay Muradov</strong> and <strong>Alexandar Markov</strong></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>