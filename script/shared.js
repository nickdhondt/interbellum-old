"use strict";

// Used the detect in the page is visible
var hidden = "hidden";
var shown = false;

// Execute code when page has been loaded
window.addEventListener("load", function(){initShared()});

function initShared(){
    if (!("Notification" in window)) {
        // Check if the browser support Notifications and display a message if it doesn't
        new WebNotification("This browser does not support system notifications");
    } else if (Notification.permission !== "denied") {
        // If the browser does support Notifications but the page does not have permission to show them, ask permission
        Notification.requestPermission();
    }

    var typeString = "";
    window.addEventListener("keyup",
        function(e){
            if(shown === false) {
                typeString+=String.fromCharCode(e.which).toLowerCase();
                if(typeString.length > 9) {
                    typeString=typeString.substr(1,9)
                }
                if(typeString.search("jef daels|copyright") !== -1) {
                    document.getElementById("jef").style.display="flex";
                    shown = true;
                }
            }
        }
    );
    // Trash
    if (1 === 2) {
        xhr.requestSuccessful();
    }
}

// XmlHttpRequest helper
var xhr = xhr || {};

xhr = {
    // Send an asynchronous http request
    sendRequest: function(action, message, url, callback) {
        // XHR object
        var xhre = new XMLHttpRequest();

        xhre.onreadystatechange = function () {
            var response;
            if (xhre.readyState === 4 && xhre.status === 200) {
                // If the server responds correctly (with a 200 OK) the response is send to the given callback function
                response = xhre.responseText;
                if(typeof callback !== "undefined") callback(response);
            } else if (xhre.readyState === 4 && xhre.status === 404) {
                // If the page cannot be found (404) the callback function will be called empty
                // Use xhr.requestSuccessful to assist in detecting if this happened
                if(typeof callback !== "undefined") callback();
            }
        };

        xhre.onerror = function() {
            // If an error occurred a notification is shown
            new WebNotification("OH! It looks like you don't have an internet connection. Or something else went wrong.", false, true);
        };

        // Send 'post' or 'get' (=action) to a page
        xhre.open(action, url);

        if (action === "post") {
            // Send post message
            xhre.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhre.send(message);
        } else {
            // Using get
            xhre.send();
        }
    },
    requestSuccessful: function(response) {
        if (typeof response !== "undefined") {
            // If there is a response, return true
            return true;
        } else {
            // If there is no response this means something went wrong
            // Show a notification and return false
            new WebNotification("OH! The request could not be completed. (Pssst. Maybe you should tell a developer.)", false, true);
            return false;
        }
    }
};

// Logic to support/enhance the working of the application
var support = support || {};

support = {
    notAllowedWarning: false,
    parseJSON: function(jsonData) {
        var parsedResponse = null;
        // Exception handling
        try {
            // If the received data is a string, try to parse it to a javascript object
            if (typeof jsonData === "string") parsedResponse = JSON.parse(jsonData);
            // If the received data is an object, do noting
            else if (typeof jsonData === "object") parsedResponse = jsonData;
        } catch (ex) {
            // If the parsing of the data causes an exception, show a notification
            new WebNotification("A server-side problem occurred, can't process data: " + ex, false, true);
            new WebNotification("Server reports: " + jsonData, false, true);
        }

        // return the data or null
        return parsedResponse;
    }
};

// Not finished
// Do not use in production
var WebNotification = function(message, persistent, forceWeb) {

    var defaultForce = false;

    if (typeof persistent === "undefined") persistent = false;
    if (typeof forceWeb === "undefined") {
        forceWeb = false;
        defaultForce = true;
    }

    this.message = message;
    this.persistent = persistent;
    this.forceWeb = forceWeb;

    if (document[hidden] && this.forceWeb === false && defaultForce === true) {
        if (Notification.permission === "granted") {
            new Notification(this.message);
        } else {
            if (support.notAllowedWarning === false) {
                new WebNotification("Notifications not allowed, consider allowing notifications.", true, true);
                support.notAllowedWarning = true;
            }
            new WebNotification(this.message, false, false, true);
        }
    } else {
        var notifContainer = document.getElementById("int_notif");

        var newNotifNode = document.createElement("div");
        var newNotifTextNode = document.createTextNode(this.message);
        newNotifNode.appendChild(newNotifTextNode);

        this.node = newNotifNode;

        var thisNotif = this;

        notifContainer.appendChild(newNotifNode);

        newNotifNode.addEventListener("click", function(){thisNotif.removeNotification()});

        if (this.persistent === false) setTimeout(function (){thisNotif.removeNotification()}, 6000);
    }

};

WebNotification.prototype.removeNotification = function() {
    var thisNotif = this;

    this.node.style.opacity = "0";

    setTimeout(function(){thisNotif.node.parentNode.removeChild(thisNotif.node);}, 200);
};