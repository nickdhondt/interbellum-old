"use strict";

// Used the detect in the page is visible
var hidden = "hidden";
var shown = false;

// Execute code when page has been loaded
window.addEventListener("DOMContentLoaded", function(){initShared()});

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
        stream.commenceLongPoll("", function(){});
        loader.getLoader();
    }
}

var suggestions = suggestions || {};

suggestions = {
    addSuggestions: function(suggestions_list, list, searchTerm, clickFunction) {
        var listNode = document.createElement("ul");
        list.activeItem = false;

        for (var i = 0; i < suggestions_list.length; i++) {
            var listItemNode = document.createElement("li");
            if (i === 0) {
                listItemNode.setAttribute("class", "selected_suggestion");
                list.activeItem = listItemNode;
            }
            listItemNode.suggestion = suggestions_list[i];
            listItemNode.innerHTML = support.highlightWords(suggestions_list[i].trim(), searchTerm.trim());

            listItemNode.addEventListener("click", function(e){clickFunction(e.target.suggestion)});

            listNode.appendChild(listItemNode);
        }

        list.appendChild(listNode);

         if (typeof list.eventsAdded === "undefined")  {
             list.parentNode.addEventListener("keydown", function (e) {
                 if (e.keyCode === 40) suggestions.highlightSuggestion(list, "down");
                 else if (e.keyCode === 38) suggestions.highlightSuggestion(list, "up");
             });

             list.parentNode.addEventListener("keyup", function (e) {
                 if (e.keyCode === 13) {
                     clickFunction(list.activeItem.suggestion);
                 }
             });
         }

        list.eventsAdded = true;
    },
    highlightSuggestion: function(list, direction) {
        if (typeof list.activeItem === "undefined" || list.activeItem === false) {
            if (direction === "down") {
                list.childNodes[0].childNodes[0].className = "selected_suggestion";
                list.activeItem = list.childNodes[0].childNodes[0];
            } else {
                list.childNodes[0].childNodes[list.childNodes[0].childNodes.length - 1].className = "selected_suggestion";
                list.activeItem = list.childNodes[0].childNodes[list.childNodes[0].childNodes.length - 1];
            }
        } else {
            var activeItem;

            if (direction === "down") {
                for (var i = 0; i < list.childNodes[0].childNodes.length; i++) {
                    if (list.activeItem === list.childNodes[0].childNodes[i] && typeof list.childNodes[0].childNodes[i + 1] !== "undefined") {
                        list.childNodes[0].childNodes[i].className = "";
                        list.childNodes[0].childNodes[i + 1].className = "selected_suggestion";

                        activeItem = list.childNodes[0].childNodes[i + 1];
                    }
                }

                if (typeof activeItem !== "undefined") list.activeItem = activeItem;
            } else {
                for (var j = 0; j < list.childNodes[0].childNodes.length; j++) {
                    if (list.activeItem === list.childNodes[0].childNodes[j] && typeof list.childNodes[0].childNodes[j - 1] !== "undefined") {
                        list.childNodes[0].childNodes[j].className = "";
                        list.childNodes[0].childNodes[j - 1].className = "selected_suggestion";

                        activeItem = list.childNodes[0].childNodes[j - 1];
                    }
                }

                if (typeof activeItem !== "undefined") list.activeItem = activeItem;
            }


        }
    }
};

var loader = loader || {};

loader = {
    getLoader: function(loaderId){
        var loaderBar = document.querySelector("#" + loaderId);

        if (typeof loaderBar !== "undefined") {
            var progress = loaderBar.dataset.progress;

            return {name: loaderId, progress: progress}
        } else {
            return {error: "Loader not found"}
        }
    },
    updateLoader: function(loaderId, progess){
        var loaderBar = document.querySelector("#" + loaderId);

        loaderBar.dataset.progress = progess;

        loaderBar.firstChild.style.width = progess * 100 + "%";
    }
};

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
    foundTag: false,
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
    },
    unixToTime: function(unixTimestamp){
        // create a new javascript Date object based on the timestamp
        // multiplied by 1000 so that the argument is in milliseconds, not seconds
        var date = new Date(unixTimestamp * 1000);
        // hours part from the timestamp
        var hours = date.getHours();
        // minutes part from the timestamp
        var minutes = "0" + date.getMinutes();
        // seconds part from the timestamp
        var seconds = "0" + date.getSeconds();

        // will display time in 10:30:23 format
        return hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
    },
    highlightWords: function(line, word) {
        var regex = new RegExp( '(' + word + ')', 'gi' );

        return line.replace( regex, "<strong>$1</strong>" );
    },
    bubbleToTag: function(tag, tagData) {
        support.foundTag = false;
        if (tagData.type === "tag") {
            if (tag.tagName.toLowerCase() === tagData.name) support.foundTag = tag;
            else if (tag.tagName.toLowerCase() !== "body") support.bubbleToTag(tag.parentNode, tagData);
        } else  if (tagData.type === "id") {
            if (tag.id.toLowerCase() === tagData.name) support.foundTag = tag;
            else if (tag.tagName.toLowerCase() !== "body") support.bubbleToTag(tag.parentNode, tagData);
        }
    }
};

var stream = stream || {};

stream = {
    streamPauseIntervalId: -1,
    commenceLongPoll: function(url, callback) {
        var poll = new XMLHttpRequest();

        poll.onreadystatechange = function () {
            var response;
            if (poll.readyState === 4 && poll.status === 200) {
                // If the server responds correctly (with a 200 OK) the response is send to the given callback function
                response = poll.responseText;
                if(typeof callback !== "undefined") callback(response);
            }
        };

        poll.onerror = function() {
            clearTimeout(stream.streamPauseIntervalId);
            // If an error occurred, a notification is shown
            new WebNotification("There was an error opening you stream. Attempting new connection in 10 seconds.", false, true);
            stream.streamPauseIntervalId = setTimeout(function(){stream.commenceLongPoll(url, callback)}, 10000);
        };

        // Send or get to a long poll page
        poll.open("get", url);
        poll.send();
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

Array.prototype.removeElement = function(element) {
    var index = this.indexOf(element);

    if (index > -1) {
        this.splice(index, 1);
    }
};