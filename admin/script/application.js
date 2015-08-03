"use strict";

// Initiate the script when the page has been loaded
window.addEventListener("load", function(){init()});

function init() {
    // Add events
    aek.initEvents();
    application.initEvents();
}

// Early access keys logic
var aek = aek || {};

aek = {
    initEvents: function(){
        // Add early access related events
        document.getElementsByTagName("input")[0].addEventListener("input", function(e){application.provideFeedback(e)});
        document.getElementsByName("btn_generate")[0].addEventListener("click", function(){aek.generateKeys()});
    },
    generateKeys: function() {
        // Obtain the amount of keys that must be generated
        var keys = document.getElementsByTagName("input")[0].value;

        // Put the number in an object
        var data = {
            "keys": keys
        };

        // Send the request to the server with the data in JSON form
        // Execute 'aek.interpretGenerateKeys' afterwards
        xhr.sendRequest("post", "data=" + JSON.stringify(data), "../server/eak/generate_keys.php", aek.interpretGenerateKeys);
    },interpretGenerateKeys: function(response) {
        // Check if the request was successful
        if (xhr.requestSuccessful(response)) {
            // Parse the JSON data to a javascript opbject
            var parsedResponse = support.parseJSON(response);

            // Check if the server executed the request correctly
            if (parsedResponse.feedback[0].legal === true) {
                // Display the generated keys in a container on the page
                application.generateKeysContainer(parsedResponse.feedback[0].keys, parsedResponse.feedback.time);
            }
        }
    }
};

// Application logic
var application = application || {};

application = {
    // Parameter for navigation in mobile mode. (Using media queries)
    navVisible: false,
    // setTimeout id
    shadeTimeoutId: -1,
    // An index which contains all pages / tabs
    // This is used when a user click on an item in the navigation menu (see application.showContent)
    pages: {
        ea: ["early_access", "Early Access"],
        users: ["users", "Users"]
    },
    initEvents: function() {
        // Add Navigation related events
        document.getElementById("hamburger").addEventListener("click", function () {application.toggleNav()});
        document.getElementById("early_access_nav").addEventListener("click", function(){application.showContent("ea")});
        document.getElementById("users_nav").addEventListener("click", function(){application.showContent("users")});
        document.getElementById("shade").addEventListener("click", function(){application.toggleNav()});
    },
    showContent: function(content) {
        // Get the content element in the header that display which page is being shown
        var headerPage = document.getElementById("page");

        // Loop through all elements in the pages index
        for (var page in application.pages) {
            if (application.pages.hasOwnProperty(page)) {
                // Hide all pages
                document.getElementById(application.pages[page][0]).style.display = "none";
            }
        }
        // Display only the clicked page
        // The content parameter can be linked to one of the application.pages property
        // The values in the selected array are being used to:
        //   1. Select the required page node (the content that needs to be displayed)
        //   2. The text that needs to be displayed in the header
        document.getElementById(application.pages[content][0]).style.display = "block";
        // Display the appropriate text in the header
        headerPage.innerHTML = "- " + application.pages[content][1];
    },
    provideFeedback: function(e) {
        // Get the value of the slider and display it in it's label
        var label = document.getElementsByTagName("label")[0];

        label.innerHTML = e.target.value;
    },
    generateKeysContainer: function(keys, time) {
        var message = document.getElementById("message");

        // Removing the notice if it is still present
        // In it's place come the keys
        if (message !== null) message.parentNode.removeChild(message);

        var newKeys = document.getElementById("new_keys");

        // Create the keys container
        var accordionNode = document.createElement("div");
        accordionNode.setAttribute("class", "accordion");

        // Create the header
        var timeNode = document.createElement("div");
        var timeTextNode = document.createTextNode(time);
        timeNode.appendChild(timeTextNode);
        accordionNode.appendChild(timeNode);

        // Create the keys object
        var keysNode = document.createElement("div");
        keysNode.setAttribute("class", "keys");

        for(var i = 0; i < keys.length; i++) {
            var keyNode = document.createElement("div");
            var keyTextNode = document.createTextNode(keys[i]);
            keyNode.appendChild(keyTextNode);

            keysNode.appendChild(keyNode);
        }

        accordionNode.appendChild(keysNode);

        // Insert new keys at the top of the list
        newKeys.insertBefore(accordionNode, newKeys.firstChild);
    },
    toggleNav: function() {
        // Get the "shade" overlay element
        // This will slightly darken and disable the page when the navigation menu is open
        var shade = document.getElementById("shade");

        if (application.navVisible === false) {
            // Cancel the timeout that has been started when closing the navigation menu
            clearTimeout(application.shadeTimeoutId);
            // Show the navigation panel
            document.getElementsByTagName("nav")[0].style.left = "0";
            shade.style.zIndex = "1";
            shade.style.opacity = "1";
            // Set the visible parameter to true
            application.navVisible = true;
        } else {
            // Close the navigation panel
            document.getElementsByTagName("nav")[0].style.left = "-80%";
            application.shadeTimeoutId = setTimeout(function(){shade.style.zIndex = "-1"}, 200);
            shade.style.opacity = "0";
            // Set the visible parameter to false
            application.navVisible = false;
        }
    }
};