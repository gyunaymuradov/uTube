$(window).resize(function() {
    respondToSize();
});

function toggleSidebar() {
    var navbar = document.getElementById("navbar");
    if(navbar.style.display === "block") {
        navbar.style.display = "none";
    }
    else {
        navbar.style.display = "block";
    }
}

function respondToSize() {
    var path = $(this);
    var contW = path.width();
    var searchBar = document.getElementById("searchBar");
    var containerSmall = document.getElementById("searchBarContainerSmall");
    var containerLarge = document.getElementById("searchBarContainerLarge");
    var searchBarParent = searchBar.parentNode;

    if(contW >= 992){
        document.getElementById("navbar").style.display = "block";
        if (searchBarParent === containerSmall) {
            containerLarge.appendChild(searchBar);
        }

    } else{
        document.getElementById("navbar").style.display = "none";
        if (searchBarParent === containerLarge) {
            containerSmall.appendChild(searchBar);
        }
    }
}