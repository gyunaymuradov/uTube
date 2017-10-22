<form method="post" action="index.php?page=edit-profile" onsubmit="event.preventDefault()">
    <div class="form-group row margin-top" id="about-page">
        <label for="username" class="col-sm-4 col-form-label col-sm-offset-2">Username</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="username" id="username" value="<?= htmlentities($params['username']); ?>">
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
    </div>
    <div class="form-group row">
        <label for="firstName" class="col-sm-4 col-form-label  col-sm-offset-2">First name</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="first-name" id="first-name" onblur="validateFirstName()" value="<?= htmlentities($params['first-name']); ?>">
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
    </div>
    <div class="form-group row">
        <label for="lastName" class="col-sm-4 col-form-label  col-sm-offset-2">Last name</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="last-name" id="last-name" value="<?= htmlentities($params['last-name']); ?>" maxlength="15">
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
    </div>
    <div class="form-group row">
        <label for="email" class="col-sm-4 col-form-label col-sm-offset-2">Email</label>
        <div class="col-sm-4">
            <input type="email" class="form-control" name="email" id="email" value="<?= htmlentities($params['email']); ?>" maxlength="15">
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
    </div>
    <div class="form-group row">
        <label for="oldPass" class="col-sm-4 col-form-label  col-sm-offset-2">Old password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" id="old-pass" name="old-pass" maxlength="15">
            <?php $msg = $params['msg']; echo "<span class='help-block'><p class='text-danger'>$msg</p></span>";
                if(!empty($params['errors']['oldpassword'])) {
                    $error = $params['errors']['oldpassword'];
                    echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                }
            ?>

        </div>
    </div>
    <div class="form-group row">
        <label for="newPass" class="col-sm-4 col-form-label  col-sm-offset-2">New password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" id="password" name="new-pass"  maxlength="20">
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
    </div>
    <div class="form-group row">
        <label for="confirmNewPass" class="col-sm-4 col-form-label  col-sm-offset-2">Confirm new password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" id="confirm-password" name="confirm-new-pass">
            <div id="confirm-password-errors">
                <?php
                if (!empty($params['errors']['confirm-pass'])) {
                    $error = $params['errors']['confirm-pass'];
                    echo "<span class='help-block'><p class='text-danger'>$error</p></span>";
                }
                ?>
            </div>
        </div>
    </div>
    <div class="form-group row text-center">
        <div class="col-md-offset-6">
            <button type="submit" class="btn btn-primary btn-md" onclick="submitEditProfile();" name="edit">Edit</button>
            <input type="hidden" name="user-id" id="user-id" value="<?= $params['user-id']; ?>">
        </div>
    </div>
</form>