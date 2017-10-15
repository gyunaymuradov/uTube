function getEditForm(userId) {
    var request = new XMLHttpRequest();
    request.open('GET', 'http://localhost/utube/controller/editForm.php?id=' + userId);
    request.onreadystatechange = function () {
    if (this.readyState === 4 && this.status === 200) {
        var div = document.getElementById('menu2');
        div.innerHTML = '';
        div.innerHTML = this.responseText;
    }
    };
    request.send();
}

function checkIfLogged() {
    var logged = document.getElementById('logged').value;
    if (logged == 'false') {
        alert('Please sign in to gain full access!');
    }
}