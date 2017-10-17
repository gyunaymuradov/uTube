function searchOption(option) {
    var hiddenField = document.getElementById('search-for');
    if (option === 'video') {
        hiddenField.value = 'video';
    } else {
        hiddenField.value = 'user';
    }
}