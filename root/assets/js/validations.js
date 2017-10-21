function validateUsername() {
    var usernameInputField = document.getElementById('username');
    var username = usernameInputField.value;
    var errors = [];
    if (username.trim().length > 0) {
        if (!hasLengthLessThan(username, 11)) {
            errors.push('Username cannot contain more than 10 characters.');
        }
        if (!hasLengthMoreThan(username, 4)) {
            errors.push('Username must be at least 5 characters long.');
        }
        if (hasWhiteSpace(username)) {
            errors.push('Username cannot contain whitespace.');
        }
    }
    // var usernameErrors = document.getElementById('username-errors');
    // usernameErrors.innerHTML = '';
    // if (errors.length > 0) {
    //     usernameInputField.style.borderColor = 'red';
    //     errors.forEach(function (e) {
    //         var erorrSpan = document.createElement('span');
    //         erorrSpan.className = 'help-block';
    //         erorrSpan.innerHTML = "<p class='text-danger'>" + e + "</p>";
    //         usernameErrors.appendChild(erorrSpan);
    //     });
    // } else {
    //     usernameInputField.style.border = '1px solid #ddd';
    //     return true;
    // }
    manipulateInputField('username-errors', errors, usernameInputField);
}

function validateFirstName() {
    var firsNameInputField = document.getElementById('first-name');
    var firstName = firsNameInputField.value;
    var errors = [];
    if (firstName.trim().length > 0) {
        if (!hasLengthLessThan(firstName, 16)) {
            errors.push('First name cannot contain more than 15 characters.');
        }
        if (!hasLengthMoreThan(firstName, 1)) {
            errors.push('First name must be at least 2 characters long.');
        }
        if (hasWhiteSpace(firstName)) {
            errors.push('First name cannot contain whitespace.');
        }
    }
    manipulateInputField('first-name-errors', errors, firsNameInputField);
}

function validateLastName() {
    var lastNameInputField = document.getElementById('last-name');
    var lastName = lastNameInputField.value;
    var errors = [];
    if (lastName.trim().length > 0) {
        if (!hasLengthLessThan(lastName, 16)) {
            errors.push('Last name cannot contain more than 15 characters.');
        }
        if (!hasLengthMoreThan(lastName, 1)) {
            errors.push('Last name must be at least 2 characters long.');
        }
        if (hasWhiteSpace(lastName)) {
            errors.push('Last name cannot contain whitespace.');
        }
    }
    manipulateInputField('last-name-errors', errors, lastNameInputField);
}

function validateEmail() {
    var emailInputField = document.getElementById('email');
    var email = emailInputField.value;
    var errors = [];
    if (email.trim().length > 0) {
        if (!hasLengthMoreThan(email, 6)) {
            errors.push('Email must be at least 7 characters long.');
        }
        if (hasWhiteSpace(email)) {
            errors.push('Email name cannot contain whitespace.');
        }
        if (!hasValidEmail(email)) {
            errors.push('Email format is not valid.');
        }
    }
    manipulateInputField('email-errors', errors, emailInputField);
}

function validatePassword() {
    var passwordInputField = document.getElementById('password');
    var confirmPassword = document.getElementById('confirm-password').value;
    var password = passwordInputField.value;
    var errors = [];
    if (password.trim().length > 0) {
        if (!hasValidPassword(password)) {
            errors.push('Password should contain at least 1 digit, 1 uppercase and 1 lowercase letters and should be at least 6 characters long.');
        }
        if (confirmPassword.length === 0) {
            errors.push('Confirm password cannot be blank.');
        }
        if (password !== confirmPassword) {
            errors.push('The passwords do not match.');
        }
    }
    manipulateInputField('password-errors', errors, passwordInputField);
}

function validateImage() {
    if (document.getElementById('photo').value !== "") {
        var fileInputField = document.getElementById('photo');
        var errors = [];
        var mimeType =  getMimeType();
        if (mimeType !== 'image/jpeg' || mimeType !== 'image/png') {
            errors.push('Allowed image types are .jpg .jpeg and .png.');
        }
        if (!hasValidFilesize()) {
            errors.push('File cannot be larger than 5 megabytes.');
        }
        manipulateInputField('file-error', errors, fileInputField);
    }
}


function getMimeType() {
    var blob = document.getElementById('photo').files[0];
    var fileReader = new FileReader();
    var header = "";
    fileReader.onloadend = function(e) {
        var arr = (new Uint8Array(e.target.result)).subarray(0, 4);
        for(var i = 0; i < arr.length; i++) {
            header += arr[i].toString(16);
        }
    };
    fileReader.readAsArrayBuffer(blob);
    var type = '';
    switch (header) {
        case "89504e47":
            return "image/png";
            break;
        case "ffd8ffe0":
        case "ffd8ffe1":
        case "ffd8ffe2":
            return "image/jpeg";
            break;
        default:
            return "unknown";
            break;
    }
}


function hasValidFilesize() {
    var file = document.getElementById('photo').files[0];
    return file.size < 5000000;
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
    } else {
        inputField.style.border = '1px solid #ddd';
        return true;
    }
}