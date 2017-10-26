function searchOption(option) {
    var hiddenField = document.getElementById('search-for');
    var searchInput = document.getElementById('search');
    if (option === 'video') {
        hiddenField.value = 'video';
        searchInput.placeholder = 'Search video';
    } else if (option === 'user') {
        hiddenField.value = 'user';
        searchInput.placeholder = 'Search user';
    } else {
        hiddenField.value = 'playlist';
        searchInput.placeholder = 'Search playlist';
    }
}

function clickListener() {
    document.body.addEventListener('click', function(e) {
        if (!e.target || e.target.className.indexOf('autocomplete-item') < 0) {
            document.getElementById('search-autocomplete').style.display = 'none';
        }
    })
}

function getSuggestions() {
        var searchOption = document.getElementById('search-for').value;
        var searchValue = document.getElementById('search').value;
        var request = new XMLHttpRequest();
        var autocompleteDiv = document.getElementById('search-autocomplete');
        if (searchValue.length > 0 && searchValue.trim().length > 0) {
            request.onreadystatechange = function () {
                if (this.readyState === 4 && this.status === 200) {
                    var response = JSON.parse(this.responseText);
                    if (response.suggestions !== undefined || response.suggestions.length !== 0) {
                        autocompleteDiv.innerHTML = '';
                        autocompleteDiv.style.display = 'block';
                        if (searchOption === 'video') {
                            response.suggestions.forEach(function (suggestion) {
                                var id = suggestion.id;
                                var title = suggestion.title;
                                var a = document.createElement('a');
                                a.className = 'autocomplete-item';
                                a.style.display = 'block';
                                a.innerHTML = title;
                                a.href = 'index.php?page=video&action=watch&id=' + id;
                                autocompleteDiv.appendChild(a);
                            });
                        } else if (searchOption === 'user') {
                            response.suggestions.forEach(function (suggestion) {
                                var id = suggestion.id;
                                var username = suggestion.username;
                                var a = document.createElement('a');
                                a.className = 'autocomplete-item';
                                a.style.display = 'block';
                                a.innerHTML = username;
                                a.href = 'index.php?page=user&action=user&id=' + id;
                                autocompleteDiv.appendChild(a);
                            });
                        } else {
                            response.suggestions.forEach(function (suggestion) {
                                var id = suggestion.id;
                                var title = suggestion.title;
                                var a = document.createElement('a');
                                a.className = 'autocomplete-item';
                                a.style.display = 'block';
                                a.innerHTML = title;
                                a.href = 'index.php?page=video&action=watch&playlist-id=' + id;
                                autocompleteDiv.appendChild(a);
                            });
                        }
                    }
                }
            };
            request.open('GET', 'index.php?page=index&action=search&search-option=' + searchOption + '&value=' + searchValue);
            request.send();
        }
        autocompleteDiv.style.display = 'none';
}