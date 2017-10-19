<form method="post" action="index.php?page=edit-profile" enctype="multipart/form-data">
    <div class="form-group row margin-top" id="about-page">
        <label for="username" class="col-sm-4 col-form-label col-sm-offset-2">Username</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="username" value="<?= $params['username']; ?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="firstName" class="col-sm-4 col-form-label  col-sm-offset-2">First name</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="firstName" value="<?= $params['firstName']; ?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="lastName" class="col-sm-4 col-form-label  col-sm-offset-2">Last name</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" name="lastName" value="<?= $params['lastName']; ?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="email" class="col-sm-4 col-form-label  col-sm-offset-2">Email</label>
        <div class="col-sm-4">
            <input type="email" class="form-control" name="email" value="<?= $params['email']; ?>">
        </div>
    </div>
    <div class="form-group row">
        <label for="newPass" class="col-sm-4 col-form-label  col-sm-offset-2">New password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" name="newPass">
        </div>
    </div>
    <div class="form-group row">
        <label for="confirmNewPass" class="col-sm-4 col-form-label  col-sm-offset-2">Confirm new password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" name="confirmNewPass">
        </div>
    </div>
    <div class="form-group row">
        <label for="oldPass" class="col-sm-4 col-form-label  col-sm-offset-2">Old password</label>
        <div class="col-sm-4">
            <input type="password" class="form-control" name="oldPass">
        </div>
    </div>
    <div class="form-group row">
        <label for="photo" class="btn btn-link col-sm-12 form-control" tabindex="2">Choose new photo</label>
        <input type="file" id="photo" name="photo" style="visibility:hidden;">
    </div>
    <div class="form-group row">
        <div class="col-md-offset-6">
            <button type="submit" class="btn btn-primary btn-md" name="edit">Edit</button>
            <input type="hidden" name="userId" value="<?= $params['userId']; ?>">
        </div>
    </div>
</form>