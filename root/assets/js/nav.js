$(window).resize(function() {
    var path = $(this);
    var contW = path.width();
    if(contW >= 992){
        document.getElementById("navbar").style.display = "block";
    }else{
        document.getElementById("navbar").style.display = "none";
    }
});

function toggleSidebar() {
    var navbar = document.getElementById("navbar");
    if(navbar.style.display == "block") {
        navbar.style.display = "none";
    }
    else {
        navbar.style.display = "block";
    }
}
