"use strict";

// Initiate the script when the page has been loaded
window.addEventListener("DOMContentLoaded", function(){init()});
window.addEventListener("load", function(){application.loading.assetsLoaded = true; application.updateInitLoader()});

function init() {
    application.loadGame();
    application.initEvents();
    newMessage.initEvents();
    newMessage.initSuggestionEvents();
    setTimeout(function(){application.initLoadDelay = true; application.hideInitLoader()}, 1000);
}

var application = application || {};

application = {
    loading: {
        assetsLoaded: false,
        openStream: false,
        initMap: false,
        initUser: false,
        basicData: false
    },
    initLoadDelay: false,
    gameLoaded: false,
    serverTimeIntervalId: -1,
    serverTime: -1,
    pagesIndex: {
        map: ["map", true],
        city: ["city", true],
        messaging: ["messaging", false],
        notifications: ["notifications", false],
        federation: ["federation", true],
        settings: ["settings", false],
        newMessage: ["new_message", false]
    },
    pagesStack: [],
    interpretInitial: function(response){
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {
                if (parsedResponse.logged_in === true) {
                    application.loading.initUser = true;
                    application.loadGame();
                    application.updateInitLoader();
                } else window.location.assign("../logged_out.php");
            }
        }
    },
    updateInitLoader: function(){
        // Todo: don't use init-function directly (poll repeats every minute)
        var steps = Object.keys(application.loading).length;
        var completed = 0;
        for (var step in application.loading) {
            if (application.loading.hasOwnProperty(step)) {
                if (application.loading[step] === true) completed++;
            }
        }

        loader.updateLoader("initial_loader", completed / steps);

        if (steps === completed) {
            application.gameLoaded = true;
            application.hideInitLoader();
        }
    },
    hideInitLoader: function() {
        if (application.gameLoaded === true && application.initLoadDelay === true) {
            setTimeout(function(){
                document.getElementById("loading").style.opacity = "0";
                document.getElementById("loading").style.pointerEvents = "none";
                setTimeout(function(){document.getElementById("loading").style.display = "none"}, 200);
            }, 200);
        }
    },
    initEvents: function() {
        document.getElementById("map_ref").addEventListener("click", function(){application.showPage("map")});
        document.getElementById("city_ref").addEventListener("click", function(){application.showPage("city")});
        document.getElementById("messaging_ref").addEventListener("click", function(){application.showPage("messaging")});
        document.getElementById("notifications_ref").addEventListener("click", function(){application.showPage("notifications")});
        document.getElementById("federation_ref").addEventListener("click", function(){application.showPage("federation")});
        document.getElementById("settings_ref").addEventListener("click", function(){application.showPage("settings")});
        document.getElementById("new_message_ref").addEventListener("click", function(){application.showPage("newMessage")});
        document.getElementById("fullscreen_ref").addEventListener("click", function(){application.toggleFullscreen()});
        window.addEventListener("resize", function(){application.delayMapResize()});
        window.addEventListener("click", function(e){application.windowClick(e)});
    },
    windowClick: function(e) {
        support.bubbleToTag(e.target, {type: "id", name: "new_recipient_box"});
        if (support.foundTag === false) {
            var recipientSuggestions = document.getElementById("recipient_suggestions");

            newMessage.assistsCache = recipientSuggestions.innerHTML;

            recipientSuggestions.style.display = "none";
            recipientSuggestions.innerHTML = "";
        }
    },
    delayMapResize: function() {
        clearTimeout(map.delayMapId);
        map.delayMapId = setTimeout(function(){map.reloadMap()}, 100);
    },
    displayServeTime: function(timestamp){
        document.getElementById("time").innerHTML = support.unixToTime(timestamp) + "<br/>";
    },
    interpretStream: function(response){
        clearInterval(application.serverTimeIntervalId);
        if (xhr.requestSuccessful(response)){
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {
                application.loading.openStream = true;
                application.loadGame();
                application.updateInitLoader();
                application.serverTime = parsedResponse[0];
                application.displayServeTime(application.serverTime);
                application.serverTime++;
                application.serverTimeIntervalId = setInterval(function(){application.displayServeTime(application.serverTime); application.serverTime++}, 1000);

                if(Object.keys(parsedResponse.messages).length !== 0) {
                    for (var thread in parsedResponse.messages) {
                        if (parsedResponse.messages.hasOwnProperty(thread)) {
                            if (!chat.chatBoxExists(thread)) {
                                if (typeof chat.hiddenThreads[thread] === "undefined") {
                                    chat.newChatBox(parsedResponse.messages[thread].subject, thread);
                                    chat.openedThreads.push(thread);
                                } else {
                                    document.getElementById("chat_bar").appendChild(chat.hiddenThreads[thread]);

                                    chat.openedThreads.push(thread);
                                    delete chat.hiddenThreads[thread];
                                }
                            }

                            for (var i = 0; i < parsedResponse.messages[thread].messages.length; i++) {
                                chat.addMessage(thread, parsedResponse.messages[thread].messages[i], Math.random(), false);

                                chat.scrollToTop(thread);
                            }
                        }
                    }
                }
            }

            stream.commenceLongPoll("../server/stream/pull.php?time=" + parsedResponse["last_poll"], application.interpretStream);
        }
    },
    showPage: function(page) {
        var pageElement = document.getElementById(application.pagesIndex[page][0]);


            for(var pageIndex in application.pagesIndex) {
                if (application.pagesIndex.hasOwnProperty(pageIndex)) {
                    if (application.pagesIndex[page][1] === true) document.getElementById(application.pagesIndex[pageIndex][0]).style.display = "none";
                    else if (application.pagesIndex[pageIndex][1] === false) document.getElementById(application.pagesIndex[pageIndex][0]).style.display = "none";
                }
            }


        if (application.pagesIndex[page][1] === false) {
            var index = application.pagesStack.indexOf(page);

            if (index > -1) {
                application.pagesStack.splice(index, 1);
            }

            application.pagesStack.push(page);
        }

        pageElement.style.display = "block";
        if (page === "map") map.setMapPosition();
    },
    loadGame: function(){
        if (application.loading.initUser === false) xhr.sendRequest("get", "", "../server/mgmt/initial.php", application.interpretInitial);
        else if (application.loading.openStream === false) stream.commenceLongPoll("../server/stream/pull.php?fast=true", application.interpretStream);
        else if (application.loading.initMap === false) map.initMap();
        else if (application.loading.basicData === false) xhr.sendRequest("get", "", "../server/data/basic_data.php", application.interpretBasicData);
    },
    interpretBasicData: function(response) {
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {
                if (parsedResponse.logged_in === true) {
                    application.loading.basicData = true;
                    application.loadGame();
                    application.updateInitLoader();

                    if (typeof parsedResponse.feedback.inbox_threads !== "undefined") {
                        var messagingWindow = document.getElementById("messaging");

                        if (parsedResponse.feedback.inbox_threads === false) {
                            var inboxNode = document.createElement("div");
                            inboxNode.setAttribute("class", "no_threads");
                            var inboxEmphasisNode = document.createElement("em");
                            var inboxTextNode = document.createTextNode("Your inbox is empty. Send some messages.");

                            inboxEmphasisNode.appendChild(inboxTextNode);
                            inboxNode.appendChild(inboxEmphasisNode);

                            messagingWindow.children[1].appendChild(inboxNode);
                        } else {
                            for(var thread in parsedResponse.feedback.inbox_threads) {
                                if (parsedResponse.feedback.inbox_threads.hasOwnProperty(thread)) {
                                    var threadNode = application.makeInboxThread(parsedResponse.feedback.inbox_threads[thread].last_message, parsedResponse.feedback.inbox_threads[thread].thread_id, parsedResponse.feedback.inbox_threads[thread].subject);

                                    messagingWindow.children[1].appendChild(threadNode);
                                }
                            }
                        }
                    }
                } else window.location.assign("../logged_out.php");
            }
        }
    },
    makeInboxThread: function(message, threadId, subject) {
        var threadNode = document.createElement("div");
        threadNode.threadId = threadId;
        threadNode.setAttribute("class", "message_thread");
        threadNode.innerHTML = "<strong>" + subject + "</strong><br />" + message;
        threadNode.addEventListener("click", function(e){
            if (chat.openedThreads.indexOf(threadId) === -1) {
                if (typeof chat.hiddenThreads[threadId] === "undefined") {
                    chat.newChatBox(subject, threadId);
                    chat.openedThreads.push(threadId);
                } else {
                    document.getElementById("chat_bar").appendChild(chat.hiddenThreads[threadId]);

                    chat.openedThreads.push(threadId);
                    delete chat.hiddenThreads[threadId];
                }
            }
        });

        return threadNode;
    },
    toggleFullscreen: function() {
        if (!document.fullscreenElement &&    // alternative standard method
            !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  // current working methods
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen();
            } else if (document.documentElement.msRequestFullscreen) {
                document.documentElement.msRequestFullscreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullscreen) {
                document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    },
    showErrors: function(field, errors){
        for(var i = 0; i < errors.length; i++) {
            field.innerHTML = errors[i] + "<br/>";
        }
    }
};

var chat = chat || {};

chat = {
    openedThreads: [],
    hiddenThreads: {},
    scrollToTop: function(thread) {
        var chatBox = document.getElementById("thr_" + thread);

        chatBox.children[1].scrollTop = chatBox.children[1].scrollHeight;
    },
    chatBoxExists: function(thread) {
        if (document.getElementById("thr_" + thread) === null) return false;
        return true;
    },
    newChatBox: function(subject, thread) {
        var chatBoxNode = document.createElement("div");
        chatBoxNode.thread = thread;
        chatBoxNode.setAttribute("id", "thr_" + thread);
        var headerNode = document.createElement("div");
        var subjectNode = document.createElement("div");
        var subjectStrongNode = document.createElement("strong");
        var subjectTextNode = document.createTextNode(subject);
        var buttonsNode = document.createElement("div");
        var maximizeNode = document.createElement("div");
        maximizeNode.setAttribute("class", "maximize");
        var closeNode = document.createElement("div");
        closeNode.setAttribute("class", "close");

        buttonsNode.appendChild(maximizeNode);
        buttonsNode.appendChild(closeNode);
        subjectStrongNode.appendChild(subjectTextNode);
        subjectNode.appendChild(subjectStrongNode);
        headerNode.appendChild(subjectNode);
        headerNode.appendChild(buttonsNode);

        closeNode.addEventListener("click", function(e){
            chat.openedThreads.removeElement(thread);

            chat.hiddenThreads[e.target.parentNode.parentNode.parentNode.thread] = e.target.parentNode.parentNode.parentNode;
            document.getElementById("chat_bar").removeChild(e.target.parentNode.parentNode.parentNode);
        });

        var bodyNode = document.createElement("div");
        var formNode = document.createElement("form");
        var inputFieldNode = document.createElement("textarea");
        inputFieldNode.setAttribute("placeholder", "Message");
        inputFieldNode.addEventListener("keyup", function(e){if (e.keyCode === 13 && e.target.value.trim().length > 0)chat.sendMessage(e.target)});
        formNode.appendChild(inputFieldNode);

        chatBoxNode.appendChild(headerNode);
        chatBoxNode.appendChild(bodyNode);
        chatBoxNode.appendChild(formNode);

        document.getElementById("chat_bar").appendChild(chatBoxNode);
    },
    sendMessage: function (textarea) {
        var messageId = Math.random();
        chat.addMessage(textarea.parentNode.parentNode.thread, textarea.value, messageId, true);
        textarea.parentNode.parentNode.children[1].scrollTop = textarea.parentNode.parentNode.children[1].scrollHeight;
        xhr.sendRequest("post", "data=" + JSON.stringify({message: textarea.value.trim(), thread: textarea.parentNode.parentNode.thread}), "../server/message/send_message.php", function(e){chat.interpretSendMessage(e, messageId)});
        textarea.value = "";
    },
    interpretSendMessage: function(response, messageId) {
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {
                if (parsedResponse.logged_in === true) {
                    console.log(parsedResponse);
                    console.log(messageId);
                } else window.location.assign("../logged_out.php");
            }
        }
    },
    addMessage: function(thread, message, messageId, own) {
        var chatBox = document.getElementById("thr_" + thread);

        var messageNode = document.createElement("div");
        var messageTextNode = document.createTextNode(message);
        messageNode.id = messageId;
        if (own === true) messageNode.setAttribute("class", "own_message message");
        else messageNode.setAttribute("class", "other_message message");

        messageNode.appendChild(messageTextNode);
        chatBox.children[1].appendChild(messageNode);
    }
};

var newMessage = newMessage || {};

newMessage = {
    waitId: -1,
    recipientEmpty: true,
    assistsCache: "",
    assistList: [],
    clearForSend: false,
    initSuggestionEvents: function(){
        document.getElementsByName("txt_recipient_new")[0].addEventListener("keyup", function(e){
            if (e.keyCode !== 40 && e.keyCode !== 38 && e.keyCode !== 13) newMessage.assistUsernames(e)
        });
        document.getElementsByName("txt_recipient_new")[0].addEventListener("focus", function(e){
            var recipientSuggestions = document.getElementById("recipient_suggestions");

            recipientSuggestions.innerHTML = newMessage.assistsCache;
            recipientSuggestions.style.display = "block";
        });
    },
    initEvents: function(){
        document.getElementsByName("txt_subject")[0].addEventListener("keyup", function(e){newMessage.clearForSend = true; newMessage.checkSubject(e.target)});
        document.getElementsByName("txt_new_message")[0].addEventListener("keyup", function(e){newMessage.clearForSend = true; newMessage.checkMessage(e.target)});
        document.getElementsByName("btn_new_message")[0].addEventListener("click", function(){newMessage.clearForSend = true; newMessage.attemptMakeNewMessage()});
    },
    attemptMakeNewMessage: function() {
        var errors = [];
        var recipients = document.getElementsByName("txt_recipient_new")[0];
        recipients.parentNode.parentNode.children[2].innerHTML = "";
        var recipientsAssistNodes = document.getElementsByClassName("new_recipient_select");
        var messageNode = document.getElementsByName("txt_new_message")[0];
        var subjectNode = document.getElementsByName("txt_subject")[0];

        newMessage.checkMessage(messageNode, true);
        newMessage.checkSubject(subjectNode, true);
        if (recipientsAssistNodes.length <= 0) {
            newMessage.clearForSend = false;
            errors.push("You must select recipients");

            application.showErrors(recipients.parentNode.parentNode.children[2], errors);
        }

        if (newMessage.clearForSend === true) {
            var recipientsList = [];

            for (var i = 0; i < recipientsAssistNodes.length; i++) {
                recipientsList.push(recipientsAssistNodes[i].username);
            }

            var newThread = {
                recipients: recipientsList,
                subject: subjectNode.value,
                message: messageNode.value
            };

            xhr.sendRequest("post", "data=" + JSON.stringify(newThread), "../server/message/message_new_thread.php", function(e){newMessage.interpretNewThread(e, messageNode.value, subjectNode.value)});
        }
    },
    interpretNewThread: function(response, message, subject){
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {
                if (parsedResponse.logged_in === true) {
                    if (typeof parsedResponse.feedback.success !== "undefined" && parsedResponse.feedback.success === true) {
                        chat.newChatBox(subject, parsedResponse.feedback.thread);
                        var messageId = Math.random();
                        chat.addMessage(parsedResponse.feedback.thread, message, messageId, true);
                        chat.openedThreads.push(parsedResponse.feedback.thread);

                        var messagingWindow = document.getElementById("messaging");

                        var threadNode = application.makeInboxThread(message, parsedResponse.feedback.thread, subject);

                        messagingWindow.children[1].insertBefore(threadNode, messagingWindow.children[1].children[1]);
                    }
                } else window.location.assign("../logged_out.php");
            }
        }
    },
    checkSubject: function(field, extensive) {
        if (typeof extensive === "undefined") extensive = false;
        var errors = [];
        if (field.value.length <= 2 && (field.value.length >= 1 || extensive)) {
            errors.push("Minimum length is 3 characters");
            newMessage.clearForSend = false;
        }

        if (field.value.length > 64) {
            errors.push("Maximum length is 64 characters");
            newMessage.clearForSend = false;
        }

        field.parentNode.children[1].innerHTML = "";

        application.showErrors(field.parentNode.children[1], errors);
    },
    checkMessage: function(field, extensive) {
        if (typeof extensive === "undefined") extensive = false;

        var errors = [];
        if (field.value.length <= 1 && (field.value.length >= 1 || extensive)) {
            errors.push("Minimum length is 2 characters");
            newMessage.clearForSend = false;
        }

        if (field.value.length > 1000) {
            errors.push("Maximum length is 1000 characters");
            newMessage.clearForSend = false;
        }

        field.parentNode.children[1].innerHTML = "";

        application.showErrors(field.parentNode.children[1], errors);
},
    assistUsernames: function(e){
        clearTimeout(newMessage.waitId);

        if (e.target.value.length >= 1) {
            newMessage.recipientEmpty = false;
            newMessage.waitId = setTimeout(function(){
                xhr.sendRequest("get", "", "../server/data/assist_usernames.php?username=" + e.target.value, function(ev){newMessage.interpretAssist(ev, e)});
            }, 10);

        } else {
            newMessage.recipientEmpty = true;
            var recipientSuggestions = document.getElementById("recipient_suggestions");

            recipientSuggestions.style.display = "none";
            recipientSuggestions.innerHTML = "";
        }
    },
    interpretAssist: function(response, e){
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {

                if (parsedResponse.logged_in === true) {
                    newMessage.assistList = parsedResponse.feedback;
                    var recipientSuggestions = document.getElementById("recipient_suggestions");

                    recipientSuggestions.innerHTML = "";
                    recipientSuggestions.style.display = "none";

                    if (newMessage.recipientEmpty === false) {
                        if (parsedResponse.feedback.length >= 1) {
                            recipientSuggestions.style.display = "block";

                            suggestions.addSuggestions(parsedResponse.feedback, recipientSuggestions, e.target.value, newMessage.makeRecipientSelect);

                        }
                    }
                } else window.location.assign("../logged_out.php");
            }
        }
    },
    makeRecipientSelect: function (username) {
        var recipientInput = document.getElementsByName("txt_recipient_new")[0];

        if (recipientInput.value.length > 0) {
            if (typeof username === "undefined" && newMessage.assistList.length >= 1) username = newMessage.assistList[0];

            var recipientSelectNode = document.createElement("div");
            var recipientSelectTextNode = document.createTextNode(username);
            recipientSelectNode.setAttribute("class", "new_recipient_select");
            recipientSelectNode.username = username;
            recipientSelectNode.addEventListener("click", function(e){e.target.parentNode.removeChild(e.target)});

            recipientSelectNode.appendChild(recipientSelectTextNode);
            recipientInput.parentNode.appendChild(recipientSelectNode);

            var recipientInputNode = document.createElement("input");
            recipientInputNode.setAttribute("type", "text");
            recipientInputNode.setAttribute("name", "txt_recipient_new");
            recipientInputNode.style.width = "5em";

            // Todo: placeholder when recipients removed
            // Todo: autofocus on recipient select
            // Todo: prevent doubles
            // Todo: add recipient select after press spacebar
            recipientInput.parentNode.appendChild(recipientInputNode);
            recipientInputNode.focus();

            recipientInput.parentNode.removeChild(recipientInput);

            newMessage.initSuggestionEvents();

            var recipientSuggestions = document.getElementById("recipient_suggestions");

            recipientSuggestions.innerHTML = "";

            recipientSuggestions.style.display = "none";
            recipientSuggestions.innerHTML = "";
        }
    }
};