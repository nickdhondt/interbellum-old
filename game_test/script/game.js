"use strict";

window.addEventListener("load", function(){init()});

function init() {
    document.addEventListener("mousedown", function() {
        dragWindow.lowerAllWindows(true);
    });

    dragWindow.dragEvents();
    dragWindow.titleButtonsEvents();
    dragWindow.taskBarEvents();
    application.activateComponents();
    browser.events();
    browser.initBrowser();
}

var dragWindow = dragWindow || {};

dragWindow = {
    clientX: null,
    clientY: null,
    lastX: 20,
    lastY: 20,
    dragElement: {},
    mouseDown: false,
    windows: document.getElementsByClassName("window"),
    dragEvents: function() {
        for (var i=0; i < dragWindow.windows.length; i++) {
            dragWindow.windows[i].addEventListener("mousedown", function(e) {
                e.stopPropagation();
                dragWindow.bubbleToMainWindow(e.target);
                dragWindow.elevateWindow();
            });

            dragWindow.windows[i].firstChild.addEventListener("mousedown", function(e){
                if (dragWindow.dragElement.className === "window") {
                    dragWindow.lowerAllWindows();
                    dragWindow.mouseDown = true;
                    dragWindow.clientX = e.clientX;
                    dragWindow.clientY = e.clientY;
                }
            });

            document.addEventListener("mouseup", function(e){
                dragWindow.mouseDown = false;
                dragWindow.dragElement.style.opacity = "1";

                if (dragWindow.dragElement.maximized === true) {
                    dragWindow.dragElement.mouseDropped = true;
                }
            });

            document.addEventListener("mousemove", function(e){
                if (dragWindow.mouseDown === true) {
                    if (e.clientY >= 5 && e.clientX >= 5 && e.clientX < (window.innerWidth - 5) && e.clientY < (window.innerHeight - document.getElementsByTagName("nav")[0].offsetHeight)) {
                        if (dragWindow.dragElement.style.opacity !== "0.75") dragWindow.dragElement.style.opacity = "0.75";
                        if (dragWindow.dragElement.maximized !== true) {
                            dragWindow.dragElement.style.left = dragWindow.dragElement.offsetLeft + (e.clientX - dragWindow.clientX) + "px";
                            dragWindow.dragElement.style.top = dragWindow.dragElement.offsetTop + (e.clientY - dragWindow.clientY) + "px";
                        } else {
                            dragWindow.dragElement.style.left = (e.clientX - (dragWindow.dragElement.previousWidth / 2)) + "px";
                            dragWindow.dragElement.style.width = dragWindow.dragElement.previousWidth + "px";
                            dragWindow.dragElement.style.height = dragWindow.dragElement.previousHeight + "px";
                            dragWindow.dragElement.maximized = false;
                            if (dragWindow.dragElement.side === true && dragWindow.dragElement.mouseDropped !== true) {
                                dragWindow.dragElement.style.top = dragWindow.dragElement.previousTop + (e.clientY - dragWindow.clientY) + "px";
                            } else {
                                dragWindow.dragElement.style.top = dragWindow.dragElement.offsetTop + (e.clientY - dragWindow.clientY) + "px";
                                dragWindow.dragElement.mouseDropped = false;
                            }
                        }


                        dragWindow.clientX = e.clientX;
                        dragWindow.clientY = e.clientY;
                    }

                    if (e.clientY < 5) {
                        if (dragWindow.dragElement.maximized !== true) {
                            dragWindow.maximizeWindow();
                            dragWindow.dragElement.style.opacity = "1";
                        }
                    } else if (e.clientX < 5) {
                        if (dragWindow.dragElement.maximized !== true) {
                            dragWindow.maximizeWindow("left");
                            dragWindow.dragElement.style.opacity = "1";
                        }
                    } else if (e.clientX >= (window.innerWidth - 5)) {
                        if (dragWindow.dragElement.maximized !== true) {
                            dragWindow.maximizeWindow("right");
                            dragWindow.dragElement.style.opacity = "1";
                        }
                    }
                }
            });
        }
    },
    lowerAllWindows: function(all) {
        for (var i=0; i < dragWindow.windows.length; i++) {
            if (all === true) {
                dragWindow.windows[i].stack++;
                dragWindow.windows[i].style.zIndex = (900 - dragWindow.windows[i].stack);
                dragWindow.windows[i].style.boxShadow = "0 2px 2px rgba(0, 0, 0, 0.2)";
                dragWindow.windows[i].firstChild.style.backgroundColor = "#e1e1e1";
            } else {
                if (dragWindow.windows[i] !== dragWindow.dragElement) {
                    dragWindow.windows[i].stack++;
                    dragWindow.windows[i].style.zIndex = (900 - dragWindow.windows[i].stack);
                    dragWindow.windows[i].style.boxShadow = "0 2px 2px rgba(0, 0, 0, 0.2)";
                    dragWindow.windows[i].firstChild.style.backgroundColor = "#e1e1e1";
                }
            }
        }
    },
    titleButtonsEvents: function() {
        for (var i=0; i < dragWindow.windows.length; i++) {
            var buttons = dragWindow.windows[i].firstChild.childNodes;

            for (var button = 1; button <= 3; button++) {
                if (button === 1) buttons[button].addEventListener("click", function(e){
                    dragWindow.dragElement.style.display = "none";
                });

                if (button === 2) buttons[button].addEventListener("click", function(e){
                    dragWindow.maximizeWindow();
                });
            }
        }
    },
    maximizeWindow: function(side) {
        if (typeof side === "undefined") side = null;
        if (dragWindow.dragElement.maximized !== true) {
            dragWindow.dragElement.maximized = true;
            dragWindow.dragElement.previousHeight = dragWindow.dragElement.offsetHeight;
            dragWindow.dragElement.previousWidth = dragWindow.dragElement.offsetWidth;
            dragWindow.dragElement.previousTop = dragWindow.dragElement.offsetTop;
            dragWindow.dragElement.previousLeft = dragWindow.dragElement.offsetLeft;
            dragWindow.dragElement.style.top = "0";
            dragWindow.dragElement.style.left = "0";
            dragWindow.dragElement.style.height = (window.innerHeight - document.getElementsByTagName("nav")[0].offsetHeight) + "px";
            dragWindow.dragElement.style.width = window.innerWidth + "px";
            dragWindow.dragElement.side = false;

            if (side === "left") {
                dragWindow.dragElement.style.width = (window.innerWidth / 2) + "px";
                dragWindow.dragElement.style.left = "0";
                dragWindow.dragElement.side = true;
            } else if (side === "right") {
                dragWindow.dragElement.style.width = (window.innerWidth / 2) + "px";
                dragWindow.dragElement.style.left = (window.innerWidth / 2) + "px";
                dragWindow.dragElement.side = true;
            }
        } else {
            dragWindow.dragElement.maximized = false;
            dragWindow.dragElement.style.width = dragWindow.dragElement.previousWidth + "px";
            dragWindow.dragElement.style.height = dragWindow.dragElement.previousHeight + "px";
            dragWindow.dragElement.style.top = dragWindow.dragElement.previousTop + "px";
            dragWindow.dragElement.style.left = dragWindow.dragElement.previousLeft + "px";
        }
    },
    taskBarEvents: function() {
        var items = [];
        items.push(document.getElementById("nav").children);
        items.push(document.getElementsByClassName("button"));

        for(var i=0; i < items.length; i++) {
            for(var j=0; j < items[i].length; j++) {
                items[i][j].addEventListener("click", function(e){
                    var windowName = windowReference.getWindowName(e.target);
                    if (windowName !== null) {
                        dragWindow.dragElement = document.getElementById(windowName);
                        dragWindow.elevateWindow();
                        dragWindow.styleWindow();
                    }
                });
            }
        }
    },
    elevateWindow: function() {
        dragWindow.dragElement.stack = -1;
        dragWindow.dragElement.style.display = "block";
        dragWindow.lowerAllWindows();
        dragWindow.dragElement.style.zIndex = "998";
        dragWindow.dragElement.style.boxShadow = "0 5px 2px rgba(0, 0, 0, 0.2)";
        dragWindow.dragElement.firstChild.style.backgroundColor = "#cdcdcd";

        if (dragWindow.dragElement.opened !== true) {
            dragWindow.dragElement.style.left = dragWindow.lastX + "px";
            dragWindow.dragElement.style.top = dragWindow.lastY + "px";

            if (dragWindow.lastX + 40 < window.innerWidth - dragWindow.dragElement.offsetWidth - 20 && dragWindow.lastY + 30 < window.innerHeight - dragWindow.dragElement.offsetHeight - 20 - document.getElementsByTagName("nav")[0].offsetHeight) {
                dragWindow.lastX += 40;
                dragWindow.lastY += 30;
            } else {
                dragWindow.lastX = 20;
                dragWindow.lastY = 20;
            }
        }

        dragWindow.dragElement.opened = true;
    },
    bubbleToMainWindow: function (elem) {
        if (elem.className === "window") {
            dragWindow.dragElement = elem;
        }
        else dragWindow.bubbleToMainWindow(elem.parentNode);
    },
    styleWindow: function(){
            dragWindow.dragElement.childNodes[2].style.height = "calc(100% - " + eval(dragWindow.dragElement.childNodes[0].offsetHeight + 22) + "px)";
    }
};

var windowReference = windowReference || {};

windowReference = {
    references: {
        a: "browser",
        0: "mail",
        1: "profile",
        2: "settings",
        3: "new_message",
        4: "ranking"
    },
    getWindowName: function(elem) {
        var windowName = null;
        for(var key in windowReference.references) {
            if (windowReference.references.hasOwnProperty(key)) {
                if (key === elem.id) windowName = windowReference.references[key];
            }
        }
        return windowName;
    }
};

var application = application || {};

application = {
    parseJSON: function(jsonData) {
        var parsedResponse = null;
        try {
            parsedResponse = JSON.parse(jsonData);
        } catch (ex) {
            console.log("Er is een serverfout opgetreden, kan data niet verwerken: " + ex);
            console.log("Server meldt: " + jsonData);
        }

        return parsedResponse;
    },
    activateComponents: function() {
        message.sendMessage();
        message.lookForUserName();
    }
};

var xhr = xhr || {};

xhr = {
    sendRequest: function(action, message, url, callback) {
        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            var response;
            if (xhr.readyState === 4 && xhr.status === 200) {
                response = xhr.responseText;
                callback(response);
            } else if (xhr.readyState === 4 && xhr.status !== 200) {
                callback();
            }
        };

        xhr.onerror = function() {
            callback();
        };

        xhr.open(action, url);

        if (action === "post") {
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send(message);
        } else {
            xhr.send();
        }
    },
    requestSuccessful: function(response) {
        if (typeof response !== "undefined") {
            //console.log(response);
            return true;
        } else {
            console.log("Fout");
            return false;
        }
    }
};

var browser = browser || {};

browser = {
    urlBarPressed: false,
    events: function() {
        document.getElementById("url").addEventListener("keypress", function(e){
            if (e.keyCode === 13) {
                window.frames[0].location.replace(e.target.value);
            }
        });
    },
    initBrowser: function() {
        var url = "http://student.howest.be/bart.delrue/";
        //window.frames[0].location.replace(url);
    }
};

var message = message || {};

message = {
    sendMessage: function() {
        document.getElementsByName("send_message")[0].addEventListener("click", function(){
            xhr.sendRequest("post", "test", "server/message/send_message.php", message.test);
        });
    },
    test: function(resp) {
        console.log(resp);
    },
    lookForUserName: function() {
        document.getElementsByName("message_recipients")[0].addEventListener("keyup", function(e){
            var inputStringLength = e.target.value.length;
            var username = e.target.value.split(",");
            var lastUsername = username.length - 1;
            if (username[lastUsername].trim().length >= 1) {
                xhr.sendRequest("post", username[lastUsername].trim(), "server/message/look_for_username.php", message.displayProposedUsernames);
            }
            if (inputStringLength <= 0) {
                var usernamesContainer = document.getElementById("message_proposed_usernames");
                usernamesContainer.innerHTML = "";
            }
        });
        document.getElementsByName("message_recipients")[0].addEventListener("keydown", function(e){
            var inputStringLength = e.target.value.length;
            var username = e.target.value.split(",");
            var lastUsername = username.length - 1;
            if (username[lastUsername].trim().length >= 1) {
                xhr.sendRequest("post", username[lastUsername].trim(), "server/message/look_for_username.php", message.displayProposedUsernames);
            }
            if (inputStringLength <= 0) {
                var usernamesContainer = document.getElementById("message_proposed_usernames");
                usernamesContainer.innerHTML = "";
            }
        });
    },
    displayProposedUsernames: function(jsonRespnse) {
        if (xhr.requestSuccessful(jsonRespnse)) {
            var proposedUsernames = application.parseJSON(jsonRespnse);
            var usernamesContainer = document.getElementById("message_proposed_usernames");
            usernamesContainer.innerHTML = "";

            if(proposedUsernames.legal === true) {
                console.log(proposedUsernames.proposed_usernames);
                for (var i=0; i < proposedUsernames.proposed_usernames.length; i++){
                    usernamesContainer.innerHTML += proposedUsernames.proposed_usernames[i] + "<br />";
                }
            }
        }
    }
};