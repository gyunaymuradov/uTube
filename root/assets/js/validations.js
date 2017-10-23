var validUsername = false;
var validFirstName = false;
var validLastName = false;
var validEmail = false;
var validPass = false;
var validImg = false;

var validTitle = false;
var validDescription = false;
var validVideo = false;

var editVideoForm = document.getElementById('edit-video-form');
if  (editVideoForm) {
    editVideoForm.addEventListener('submit', submitEditVideo);
}

function submitEditVideo(e) {
    if (!validTitle || !validDescription) {
        e.preventDefault();
        return false;
    }
}

var uploadForm = document.getElementById('upload-form');
if  (uploadForm) {
    uploadForm.addEventListener('submit', submitUpload);
}


function submitUpload(ev) {
    if (!validTitle || !validDescription || !validVideo) {
        ev.preventDefault();
        return false;
    }
}

var registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.addEventListener('submit', submitRegister);
}

function submitRegister(e) {
    if (!validUsername || !validFirstName || !validLastName || !validEmail || !validPass) {
        e.preventDefault();
        return false;
    }
    if (document.getElementById('photo').value !== "" && !validImg) {
        e.preventDefault();
        return false;
    }
}

function validateUsername() {
    var usernameInputField = document.getElementById('username');
    var username = usernameInputField.value;
    var errors = [];

    if (!hasLengthLessThan(username, 11)) {
        errors.push('Username cannot contain more than 10 characters.');
    }
    if (!hasLengthMoreThan(username, 4)) {
        errors.push('Username must be at least 5 characters long.');
    }
    if (hasWhiteSpace(username)) {
        errors.push('Username cannot contain whitespace.');
    }
    if (hasSpecialChars(username)) {
        errors.push('Username cannot contain special characters.');
    }
    validUsername = manipulateInputField('username-errors', errors, usernameInputField);
}

function validateFirstName() {
    var firsNameInputField = document.getElementById('first-name');
    var firstName = firsNameInputField.value;
    var errors = [];
    if (!hasLengthLessThan(firstName, 16)) {
        errors.push('First name cannot contain more than 15 characters.');
    }
    if (!hasLengthMoreThan(firstName, 1)) {
        errors.push('First name must be at least 2 characters long.');
    }
    if (hasWhiteSpace(firstName)) {
        errors.push('First name cannot contain whitespace.');
    }
    if (hasSpecialChars(firstName)) {
        errors.push('First name cannot contain special characters.');
    }
    validFirstName = manipulateInputField('first-name-errors', errors, firsNameInputField);
}

function validateLastName() {
    var lastNameInputField = document.getElementById('last-name');
    var lastName = lastNameInputField.value;
    var errors = [];
    if (!hasLengthLessThan(lastName, 16)) {
        errors.push('Last name cannot contain more than 15 characters.');
    }
    if (!hasLengthMoreThan(lastName, 1)) {
        errors.push('Last name must be at least 2 characters long.');
    }
    if (hasWhiteSpace(lastName)) {
        errors.push('Last name cannot contain whitespace.');
    }
    if (hasSpecialChars(lastName)) {
        errors.push('Last name cannot contain special characters.');
    }
   validLastName = manipulateInputField('last-name-errors', errors, lastNameInputField);
}

function validateEmail() {
    var emailInputField = document.getElementById('email');
    var email = emailInputField.value;
    var errors = [];
    if (!hasLengthMoreThan(email, 6)) {
        errors.push('Email must be at least 7 characters long.');
    }
    if (hasWhiteSpace(email)) {
        errors.push('Email name cannot contain whitespace.');
    }
    if (!hasValidEmail(email)) {
        errors.push('Email format is not valid.');
    }
    validEmail = manipulateInputField('email-errors', errors, emailInputField);
}

function validatePassword() {
    var passwordInputField = document.getElementById('password');
    var confirmPassword = document.getElementById('confirm-password').value;
    var password = passwordInputField.value;
    var errors = [];
    displayConfirmPassErr();
    if (!hasValidPassword(password)) {
        errors.push('Password should contain at least 1 digit, 1 uppercase and 1 lowercase letters and should be at least 6 characters long.');
    }
    if (!hasLengthLessThan(password, 21)) {
        errors.push('Password cannot contain more than 20 characters.');
    }
    if (password !== confirmPassword) {
        errors.push('The passwords do not match.');
    }
   validPass = manipulateInputField('password-errors', errors, passwordInputField);
}

function validateImage() {
    if (document.getElementById('photo').value !== "") {
        getMimeTypeOfImg(validateImageCallback);
    }
}

function validateImageCallback(mimeType) {
    var fileInputField = document.getElementById('photo');
    var errors = [];
    if (mimeType !== "image/jpeg" && mimeType !== "image/png" && mimeType !== "image/jpg") {
        errors.push('Allowed image types are .jpg .jpeg and .png.');
    }
    if (!hasValidFilesize(5000000, 'photo')) {
        errors.push('File cannot be larger than 5 megabytes.');
    }
   validImg = manipulateInputField('file-error', errors, fileInputField);
}

function getMimeTypeOfImg(callback) {
    var blob = document.getElementById('photo').files[0];
    var fileReader = new FileReader();
    var header = "";
    fileReader.onloadend = function(e) {
        var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
        for(var i = 0; i < arr.length; i++) {
            header += arr[i].toString(16);
        }
        var type = '';
        switch (header) {
            case "89504e47":
                type = "image/png";
                break;
            case "ffd8ffe0":
            case "ffd8ffe1":
            case "ffd8ffe2":
                type = "image/jpeg";
                break;
            default:
                type = "unknown";
                break;
        }
        callback(type);
    };
    fileReader.readAsArrayBuffer(blob);
}



function validateTitle() {
    var titleInputField = document.getElementById('title');
    var title = titleInputField.value;
    var errors = [];
    if (!hasLengthLessThan(title, 71)) {
        errors.push('Title cannot contain more than 70 characters.');
    }
    if (!hasLengthMoreThan(title, 4)) {
        errors.push('Title must be at least 5 characters long.');
    }
    validTitle = manipulateInputField('title-errors', errors, titleInputField);
}

function validateDescription() {
    var descriptionInputField = document.getElementById('description');
    var description = descriptionInputField.value;
    var errors = [];
    if (!hasLengthLessThan(description, 256)) {
        errors.push('Description cannot contain more than 255 characters.');
    }
    if (!hasLengthMoreThan(description, 4)) {
        errors.push('Description must be at least 5 characters long.');
    }
    validDescription = manipulateInputField('description-errors', errors, descriptionInputField);
}

function validateVideoCallback (mimeType) {
    var videoFileInputField = document.getElementById('video-file');
    var errors = [];
    if (mimeType !== 'video/mp4' && mimeType !== 'video/webm' && mimeType !== 'video/ogg') {
        errors.push('Allowed video types are .mp4 .webm and .ogg.');
    }
    if (!hasValidFilesize(52428800, 'video-file')) {
        errors.push('File cannot be larger than 50 megabytes.');
    }
   validVideo = manipulateInputField('video-file-errors', errors, videoFileInputField);
}

function getMimeTypeOfVideo(callback) {
    var blob = document.getElementById('video-file').files[0];
    var fileReader = new FileReader();
    var header = "";
    fileReader.onloadend = function(e) {
        var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
        for(var i = 0; i < arr.length; i++) {
            header += arr[i].toString(16);
        }
        var type = '';
        switch (header) {
            case "4f676753":
                type = "video/ogg";
                break;
            case "1a45dfa3":
                type = "video/webm";
                break;
            case "00018":
                type = "video/mp4";
                break;
            default:
                type = "unknown";
                break;
        }
        callback(type);
    };
    fileReader.readAsArrayBuffer(blob);
}

function validateVideo() {
    if (document.getElementById('video-file').value !== "") {
        getMimeTypeOfVideo(validateVideoCallback);
    }
}


function hasValidFilesize(value, fieldId) {
    var file = document.getElementById(fieldId).files[0];
    return file.size < value;
}

function hasSpecialChars(value) {
    return /[-!$%^&*()_+|~=`{}\[\]:";'<>?,.\/]/.test(value);
}

function hasLengthMoreThan(string, length) {
    return string.length > length;
}

function hasLengthLessThan(string, length) {
    return string.length < length;
}

function hasWhiteSpace(string) {
    return /\s/.test(string);
}

function hasValidEmail(email) {
    return /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/.test(email);
}

function hasValidPassword(pass) {
    return /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/.test(pass);
}

function manipulateInputField(errorContainer, array, inputField) {
    var errorDiv = document.getElementById(errorContainer);
    errorDiv.innerHTML = '';
    if (array.length > 0) {
        inputField.style.borderColor = 'red';
        array.forEach(function (e) {
            var erorrSpan = document.createElement('span');
            erorrSpan.className = 'help-block';
            erorrSpan.innerHTML = "<p class='text-danger'>" + e + "</p>";
            errorDiv.appendChild(erorrSpan);
        });
        return false;
    } else {
        inputField.style.border = '1px solid #ddd';
        return true;
    }
}

function displayConfirmPassErr() {
    var confirmPassword = document.getElementById('confirm-password');
    var errorDiv = document.getElementById('confirm-password-errors');
    errorDiv.innerHTML = '';
    if (confirmPassword.value.length === 0 && document.getElementById('password').value.length > 0) {
        confirmPassword.style.borderColor = 'red';
        var erorrSpan = document.createElement('span');
        erorrSpan.className = 'help-block';
        erorrSpan.innerHTML = "<p class='text-danger'>Confirm password cannot be blank.</p>";
        errorDiv.appendChild(erorrSpan);
    } else {
        confirmPassword.style.border = '1px solid #ddd';
        return true;
    }
}
