window.addEventListener("DOMContentLoaded", function(){init()});

var dark = false;

function init() {
    document.getElementById("dark").addEventListener("click", function(){toggleCSS()});
}

function toggleCSS() {
    if(dark === false) {
        document.getElementById("pagestyle").setAttribute("href", "css/screen-dark.css");
        dark = true;
    } else {
        document.getElementById("pagestyle").setAttribute("href", "css/screen.css");
        dark = false;
    }
}