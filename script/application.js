"use strict";

// Execute code when page has been loaded
window.addEventListener("load", function(){init()});

function init(){
    // Initiate events
    application.initEvents();
    earlyAccess.initEvents();
    register.initEvents();
    login.initEvents();
    // Check if the key is valid (if one has been entered, see earlyAccess.checkKey())
    earlyAccess.checkKey();
}

// This logic makes the application
var application = application || {};

application = {
    // Namespace global parameters
    navVisible: false,
    loginVisible: false,
    preregisterVisible: false,
    registerVisible: false,
    optionsVisible: false,
    initEvents: function() {
        // Initiate events
        document.getElementById("hamburger").addEventListener("click", function(){application.toggleNav()});
        document.getElementById("register").addEventListener("click", function(){application.togglePreregister()});
        document.getElementById("shade").addEventListener("click", function() {application.clearPopups()});
        document.getElementById("signin_popup").addEventListener("click", function(e) {e.stopPropagation();});
        document.getElementById("preregister_popup").addEventListener("click", function(e) {e.stopPropagation();});
        document.getElementById("register_popup").addEventListener("click", function(e) {e.stopPropagation();});

        if(document.getElementById("options") !== null) document.getElementById("options").addEventListener("click", function(){application.toggleOptions()});
        else if(document.getElementById("login") !== null) document.getElementById("login").addEventListener("click", function(){application.toggleLogin()});
        if (document.getElementById("signout") !== null) document.getElementById("signout").addEventListener("click", function(){application.signOut()});

    },
    signOut: function() {
        // Send a request to sign the user out and execute application.afterSignOutActions afterwards
        xhr.sendRequest("get", "", "server/login/sign_out.php", application.afterSignOutActions);
    },
    afterSignOutActions: function() {
        // Generate the nodes for the logged in menu and put them in the options node
        var signInNode = application.generateSignIn();

        var optionsContainer = document.getElementById("options_container");

        optionsContainer.innerHTML = "";

        optionsContainer.appendChild(signInNode);

        // Add a listener to be able to open and close the menu
        document.getElementById("login").addEventListener("click", function(){application.toggleLogin()});
    },
    toggleOptions: function() {
        // Change the css display and switch the application.optionsVisible parameter
        if (application.optionsVisible === false) {
            document.getElementById("options_panel").style.display = "block";
            application.optionsVisible = true;
        } else {
            document.getElementById("options_panel").style.display = "none";
            application.optionsVisible = false;
        }
    },
    toggleNav: function() {
        // For mobile only (media queries)
        // Change the css display and switch the application.navVisible parameter
        if (application.navVisible === false) {
            document.getElementsByTagName("nav")[0].children[1].style.display = "inline-block";
            application.navVisible = true;

        } else {
            document.getElementsByTagName("nav")[0].children[1].style.display = "none";
            application.navVisible = false;
        }
    },
    toggleLogin: function() {
        // Enable or disable the login popup and switch the application.loginVisible parameter
        if (application.loginVisible === false) {
            application.enablePopup("signin_popup");
            application.loginVisible = true;
        } else {
            application.disablePopup("signin_popup");
            application.loginVisible = false;
        }
    },
    enablePopup: function(popup) {
        // Change the style of the popup
        document.getElementById("shade").style.display = "inline-flex";
        document.getElementById(popup).style.display = "block";
        document.getElementById(popup).style.top = "0";
        document.getElementById(popup).style.opacity = "1";
    },
    disablePopup: function(popup) {
        // Change the style of the popup
        document.getElementById("shade").style.display = "none";
        document.getElementById(popup).style.display = "none";
        document.getElementById(popup).style.top = "150px";
        document.getElementById(popup).style.opacity = "0";
    },
    clearPopups: function() {
        // Hide all popups and reset their parameters
        application.disablePopup("signin_popup");
        application.loginVisible = false;
        application.disablePopup("preregister_popup");
        application.preregisterVisible = false;
        application.disablePopup("register_popup");
        application.registerVisible = false;
    },
    togglePreregister: function() {
        // Enable or disable the pre register (early access key) popup and switch the application.preregisterVisible parameter
        if (application.loginVisible === false) {
            application.enablePopup("preregister_popup");
            application.preregisterVisible = true;
        } else {
            application.disablePopup("preregister_popup");
            application.preregisterVisible = false;
        }
    },
    toggleRegister: function() {
        // Enable or disable the register popup and switch the application.registerVisible parameter
        if (application.loginVisible === false) {
            application.enablePopup("register_popup");
            application.registerVisible = true;
        } else {
            application.disablePopup("register_popup");
            application.registerVisible = false;
        }
    },
    generateOptions: function(username, permission) {
        // Generate the options menu a user has when logged in

        // Generate the visible button
        var buttonNode = document.createElement("div");
        var buttonContentNode = document.createElement("span");
        buttonContentNode.setAttribute("id", "options");
        buttonContentNode.setAttribute("class", "href");
        var buttonContentTextNode = document.createTextNode("Welcome, " + username);
        buttonContentNode.appendChild(buttonContentTextNode);
        buttonNode.appendChild(buttonContentNode);

        var options = [];

        // Make the options index based on the permission
        if (typeof permission !== "undefined") {
            options = [
                ["Continue", true, "game", false],
                ["Account", true, "account", true],
                [permission, true, "admin", true],
                ["Sign out", false, "signout"]
            ];
        } else {
            options = [
                ["Continue", true, "game"],
                ["Account", true, "account"],
                ["Sign out", false, "signout"]
            ];
        }

        var optionsNode = document.createElement("div");
        optionsNode.setAttribute("id", "options_panel");
        var optionsContentNode = document.createElement("ul");

        // Loop through the options array and create a node for each option
        for(var i = 0; i < options.length; i++) {
            var optionsContentListItemNode = document.createElement("li");

            var optionsContentListItemContainerNode = document.createElement("div");
            optionsContentListItemContainerNode.setAttribute("id", options[i][2]);
            var optionsContentListItemContainerTextNode = document.createTextNode(options[i][0]);
            optionsContentListItemContainerNode.appendChild(optionsContentListItemContainerTextNode);

            // Based on the parameters, decide if the node should be a hyperlink, if it should open in a new tab or not or if the node should be javascript activated button
            if (options[i][1] === true) {
                var optionsContentListItemRefNode = document.createElement("a");
                optionsContentListItemRefNode.setAttribute("href", options[i][2]);
                if(options[i][3] === true) optionsContentListItemRefNode.setAttribute("target", "_blank");
                optionsContentListItemRefNode.appendChild(optionsContentListItemContainerNode);
                optionsContentListItemNode.appendChild(optionsContentListItemRefNode);
            } else {
                optionsContentListItemNode.appendChild(optionsContentListItemContainerNode);
            }

            optionsContentNode.appendChild(optionsContentListItemNode);
        }

        optionsNode.appendChild(optionsContentNode);

        // Return all nodes
        return [buttonNode, optionsNode];
    },
    generateSignIn: function() {
        // Generate th 'sing in' button used in the navigation
        var buttonNode = document.createElement("div");
        var buttonContentNode = document.createElement("span");
        buttonContentNode.setAttribute("id", "login");
        buttonContentNode.setAttribute("class", "href");
        var buttonContentTextNode = document.createTextNode("Sign in");

        buttonContentNode.appendChild(buttonContentTextNode);
        buttonNode.appendChild(buttonContentNode);

        return buttonNode;
    }
};

// Code used to sign in
var login = login || {};

login = {
    initEvents: function() {
        // Initiate events
        document.getElementById("btn_login").addEventListener("click", function(){login.attemptLogin()});
    },
    attemptLogin: function() {
        // Get the user entered values and put it in an object
        var username = document.getElementsByName("txt_l_username")[0];
        var password = document.getElementsByName("txt_l_password")[0];
        var remember = false;
        if (document.getElementsByName("chk_l_remember")[0].checked) remember = true;

        // Check if the username and password have been entered
        if (username.value.length > 0 && password.value.length > 0) {
            var loginData = {
                username: username.value,
                password: password.value,
                remember: remember
            };

            // Send a XHR request and execute login.interpretLoginAttempt afterwards
            xhr.sendRequest("post", "data=" + JSON.stringify(loginData), "server/login/login.php", login.interpretLoginAttempt)
        } else {
            // Display an error if a username and a password have not been entered
            login.displaySignInErrors(["Please enter a username and password"]);
        }
    },
    interpretLoginAttempt: function(response) {
        // If the request is successful (data has been received)
        if (xhr.requestSuccessful(response)) {
            // Parse the JSON data
            var parsedResponse = support.parseJSON(response);

            // If the server responds the request was legal, proceed
            if (parsedResponse.feedback[0].legal === true) {
                // Close the sign in popup
                application.clearPopups("signin_popup");

                // Generate and show the options a user has when logged in
                // Options  will be put in the options menu
                var optionsNodes = application.generateOptions(parsedResponse.feedback[0].user_data.username, parsedResponse.feedback[0].user_data.permission);

                var optionsContainer = document.getElementById("options_container");

                optionsContainer.innerHTML = "";

                optionsContainer.appendChild(optionsNodes[0]);
                optionsContainer.appendChild(optionsNodes[1]);

                // Add event listeners to the options menu toggle button and sign out button
                document.getElementById("options").addEventListener("click", function(){application.toggleOptions()});
                document.getElementById("signout").addEventListener("click", function(){application.signOut()});
                application.optionsVisible = false;

                // Clear the notice field node
                var noticeField = document.getElementById("signin_notice");
                noticeField.innerHTML = "";
                noticeField.style.display = "none";

                document.getElementsByName("txt_l_password")[0].value = "";
            } else {
                // If the request was not legal,
                login.displaySignInErrors(parsedResponse.feedback[0].errors)
            }
        }
    },
    displaySignInErrors: function(errors) {
        // Clear the errors node and put new errors inside
        var noticeField = document.getElementById("signin_notice");
        noticeField.innerHTML = "";
        noticeField.style.display = "block";

        for (var i = 0; i < errors.length; i++) {
            noticeField.innerHTML += errors[i] + "<br />";
        }
    }
};

// Register logic
var register = register || {};

register = {
    usernameErrors: [],
    emailErrors: [],
    passwordErrors: [],
    repeatErrors: [],
    termsErrors: [],
    clearForRegister: true,
    waitId: -1,
    initEvents: function() {
        // Initializing events
        document.getElementById("btn_register_back").addEventListener("click", function(){application.clearPopups(); application.togglePreregister()});
        document.getElementById("btn_register").addEventListener("click", function(){register.attemptRegister()});
        document.getElementsByName("txt_username")[0].addEventListener("keyup", function(e){register.checkData(e.target)});
        document.getElementsByName("txt_pass")[0].addEventListener("keyup", function(e){register.checkData(e.target)});
        document.getElementsByName("txt_repeat")[0].addEventListener("keyup", function(e){register.checkData(e.target)});
        document.getElementsByName("txt_email")[0].addEventListener("keyup", function(e){register.checkData(e.target)});
        document.getElementsByName("chk_terms")[0].addEventListener("change", function(e){register.checkData(e.target)});
    },
    attemptRegister: function() {
        // Gather and check the data
        var inputFields = [
            document.getElementsByName("txt_username")[0],
            document.getElementsByName("txt_pass")[0],
            document.getElementsByName("txt_repeat")[0],
            document.getElementsByName("txt_email")[0],
            document.getElementsByName("chk_terms")[0]
        ];

        for (var i = 0; i < inputFields.length; i++) {
            var lastField = false;
            if (i === inputFields.length - 1) lastField = true;
            // This function will actually send the request
            register.checkData(inputFields[i], true, lastField);
        }
    },
    interpretRegisterAttempt: function(response){
        // Proceed if the request is successful
        if (xhr.requestSuccessful(response)) {
            // Parse the JSON string
            var parsedResponse = support.parseJSON(response);

            // Proceed if the server responds with legal === true
            if (parsedResponse.legal === true) {
                register.displayServerErrors(parsedResponse);

                // Clear the register popup and display a message when te user has been successfully registered
                if (parsedResponse.fields.registered === true) {
                    application.clearPopups("register_popup");
                    new WebNotification("You have been registered successfully");
                }
            }
        }
    },
    checkTerms: function(element) {
        // Show an error message if the terms have not been accepted  and set the register.clearForRegister to false
        if (!element.checked) {
            // Add the error to the array list
            register.termsErrors.push("You must agree with the terms and conditions. We own you now!");
            register.clearForRegister = false;
        }
        // Display the errors
        register.changeNoticeField(element, register.termsErrors);
    },
    checkUsername: function(element, extensive) {
        // Check if the username is allowed
        // Pattern for regex
        var pattern = /[^a-zA-Z0-9\-_\.éàùèç]/g;

        // Test the username to the pattern
        if (pattern.test(element.value)) {
            register.usernameErrors.push("No special characters are allowed");
            register.clearForRegister = false;
        }
        // Check te length
        if (element.value.length > 16) {
            register.usernameErrors.push("Maximum length is 16 characters");
            register.clearForRegister = false;
        }
        if (element.value.length < 3) {
            register.usernameErrors.push("Minimum length is 3 characters");
            register.clearForRegister = false;
        }

        // Display the errors
        register.changeNoticeField(element, register.usernameErrors);

        // Put the username in an object and perform a data poll (does the username exists server side?)
        // Only if extensive is set tot true
        var userData = {
            username: element.value
        };

        if (extensive !== true) register.sendDataPoll(userData);
    },
    checkEmail: function(element, extensive) {
        // Check if the email address is allowed
        // Pattern for regex
        var pattern = /[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/g;

        // Test the address to the pattern
        if (!pattern.test(element.value)) {
            register.emailErrors.push("Please enter a valid email address");
            register.clearForRegister = false;
        }
        // Check the length
        if (element.value.length > 254) {
            register.usernameErrors.push("An email address can't be longer than 254 characters");
            register.clearForRegister = false;
        }

        // Display errors
        register.changeNoticeField(element, register.emailErrors);

        // Put the email address in an object and perform a data poll (does the email address exists server side?)
        // Only if extensive is set tot true
        var userData = {
            email: element.value
        };

        if (extensive !== true) register.sendDataPoll(userData);
    },
    checkPassword: function(element) {
        // check is the password is allowed
        if (element.value.length < 6) {
            register.passwordErrors.push("Your password must at least 6 characters long");
            register.clearForRegister = false;
        }
        if (element.value.length > 32) {
            register.passwordErrors.push("Your password can't be longer than 32 characters");
            register.clearForRegister = false;
        }

        // Display errors
        register.changeNoticeField(element, register.passwordErrors);
    },
    checkPasswordRepeat: function(element, helper) {
        // Check if the password repeat matches the first
        if (element.value !== helper.value) {
            register.repeatErrors.push("Your passwords must match");
            register.clearForRegister = false;
        }

        // Display errors
        register.changeNoticeField(element, register.repeatErrors);
    },
    sendDataPoll: function(userData) {
        // Clear the timeout if a new data poll is being requested
        if (register.waitId !== -1) clearTimeout(register.waitId);

        // Send the data poll using XmlHttpRequest
        // Execute register.interpretDataPoll after the request
        register.waitId = setTimeout(function(){
            xhr.sendRequest("post", "data=" + JSON.stringify(userData), "server/register/check_user_data.php", register.interpretDataPoll);
        }, 400);
    },
    interpretDataPoll: function(response) {
        // Proceed if the request is successful
        if (xhr.requestSuccessful(response)) {
            // Parse the JSON
            var parsedResponse = support.parseJSON(response);

            // If the server responds the request is legal display the server sent errors
            if (parsedResponse.legal === true) {
                // Display errors (if any)
                register.displayServerErrors(parsedResponse);
            }
        }
    },
    checkData: function(e, extensive, lastField) {
        var forceCheck = false;
        if (typeof extensive !== "undefined") forceCheck = true;
        if (typeof extensive === "undefined" && e.value.length >= 1) forceCheck = true;
        if (typeof extensive === "undefined") extensive = false;
        if (typeof lastField === "undefined") lastField = false;

        // Reset all parameters and errors
        register.clearForRegister = true;
        register.usernameErrors.length = 0;
        register.emailErrors.length = 0;
        register.passwordErrors.length = 0;
        register.repeatErrors.length = 0;
        register.termsErrors.length = 0;

        register.changeNoticeField(e, "");

        if (e.name === "txt_pass") register.changeNoticeField(document.getElementsByName("txt_repeat")[0], "");

        // forceCheck only true when extensive parameter is give and when filling in the form (no when pushing 'register' button)
        if (forceCheck === true) {
            if (e.name === "txt_username") register.checkUsername(e, extensive);
            else if (e.name === "txt_email") register.checkEmail(e, extensive);
            else if (e.name === "txt_pass") register.checkPassword(e);
            else if (e.name === "chk_terms") register.checkTerms(e);
            if (e.name === "txt_repeat" || e.name === "txt_pass") {
                register.checkPasswordRepeat(document.getElementsByName("txt_repeat")[0], document.getElementsByName("txt_pass")[0]);
            }
        }

        // Put all data in an objext and send a XmlHttpRequest when extensive is true
        if (extensive === true && lastField === true && register.clearForRegister === true) {
            var termsChecked = false;
            if (document.getElementsByName("chk_terms")[0].checked) termsChecked = true;
            var userData = {
                key: earlyAccess.userKey,
                username: document.getElementsByName("txt_username")[0].value,
                password: document.getElementsByName("txt_pass")[0].value,
                pass_repeat: document.getElementsByName("txt_repeat")[0].value,
                email: document.getElementsByName("txt_email")[0].value,
                terms: termsChecked
            };
            // Execute register.interpretRegisterAttempt afterwards
            xhr.sendRequest("post", "data=" + JSON.stringify(userData), "server/register/register.php", register.interpretRegisterAttempt);
        }
    },
    selectNoticeField: function(inputField) {
        // Iterate to notice field
        var noticeField = inputField.nextSibling;
        if (noticeField.nodeType != 1) {
            noticeField = noticeField.nextSibling;
        }

        return noticeField;
    },
    changeNoticeField: function(inputField, messages) {
        var noticeField = register.selectNoticeField(inputField);

        if (messages.length <= 0) {
            // If there are no messages to show: hide and empty the notice field
            noticeField.style.display = "none";
            noticeField.innerHTML = "";
        } else {
            // Show the field and insert the error messages
            noticeField.style.display = "block";
            noticeField.innerHTML = register.prepareErrors(messages);
        }
    },
    displayServerErrors: function(parsedResponse) {
        // Display errors in the appropriate notice field

        // Check errors are received
        if (typeof parsedResponse.fields !== "undefined") {
            // Loop through all the fields (types of errors)
            for (var field in parsedResponse.fields) {
                // Check if there are errors for the current field
                if (parsedResponse.fields.hasOwnProperty(field) && typeof parsedResponse.fields[field].field !== "undefined" && parsedResponse.fields[field].legal === false) {
                    // Append the errors to the appropriate array and display them using register.changeNoticeField
                    if (parsedResponse.fields[field].field === "username") {
                        var usernameField = document.getElementsByName("txt_username")[0];

                        register.appendErrors(parsedResponse.fields[field].errors, register.usernameErrors);
                        register.changeNoticeField(usernameField, register.usernameErrors);
                    } else if (parsedResponse.fields[field].field === "email") {
                        var emailField = document.getElementsByName("txt_email")[0];

                        register.appendErrors(parsedResponse.fields[field].errors, register.emailErrors);
                        register.changeNoticeField(emailField, register.emailErrors);
                    } else if (parsedResponse.fields[field].field === "password") {
                        var passwordField = document.getElementsByName("txt_pass")[0];

                        register.appendErrors(parsedResponse.fields[field].errors, register.passwordErrors);
                        register.changeNoticeField(passwordField, register.passwordErrors);
                    } else if (parsedResponse.fields[field].field === "pass_repeat") {
                        var passRepeatField = document.getElementsByName("txt_repeat")[0];

                        register.appendErrors(parsedResponse.fields[field].errors, register.repeatErrors);
                        register.changeNoticeField(passRepeatField, register.repeatErrors);
                    } else if (parsedResponse.fields[field].field === "terms") {
                        var termsCheckbox = document.getElementsByName("chk_terms")[0];

                        register.appendErrors(parsedResponse.fields[field].errors, register.termsErrors);
                        register.changeNoticeField(termsCheckbox, register.termsErrors);
                    } else if (parsedResponse.fields[field].field === "general") {
                        /*




                         !!! Needs fix !!!




                         */
                        new WebNotification(parsedResponse.fields[field].errors);
                    }
                }
            }
        }
    },
    appendErrors: function(errors, array) {
        // Loop though the received extra errors and push them to the end of the global errors array
        for(var i = 0; i < errors.length; i++) {
            array.push(errors[i]);
        }
    },
    prepareErrors: function(errors) {
        // Stringify all messages (convert from array)
        var errorString = "";

        if (typeof errors !== "undefined") {
            for (var i = 0; i < errors.length; i++) {
                errorString += errors[i] + "<br/>";
            }
        }

        return errorString;
    }
};

// Early access logic
var earlyAccess = earlyAccess || {};

earlyAccess = {
    keyValid: false,
    userKey: "",
    initEvents: function() {
        // Initiate events
        var eakFields = document.getElementsByName("txt_eak");

        for (var i = 0; i < eakFields.length; i++) {
            eakFields[i].addEventListener("keyup", function(e){
                earlyAccess.assistActions(e);
                earlyAccess.checkKey();
            })
        }

        document.getElementById("btn_eak").addEventListener("click", function(){earlyAccess.attemptRegisterProgress()});
    },
    attemptRegisterProgress: function() {
        if (earlyAccess.keyValid === true) {
            application.clearPopups();
            application.toggleRegister();
        } else {
            new WebNotification("Your key is not valid");
        }
    },
    assistActions: function(e) {
        if (e.target.value.length >= 4) {
            e.target.value = e.target.value.substr(0, 4);
            var nextListItem = e.target.parentNode.nextSibling;

            if (nextListItem.nodeType != 1) {
                nextListItem = nextListItem.nextSibling;
            }

            if (nextListItem.children[0].type === "text" || nextListItem.children[0].type === "button") {
                nextListItem.children[0].select();
            }
        }
    },
    checkKey: function() {
        var eakFields = document.getElementsByName("txt_eak");
        earlyAccess.userKey = "";

        for (var i = 0; i < eakFields.length; i++) {
            earlyAccess.userKey += eakFields[i].value;
        }

        if (earlyAccess.userKey.length >= 16) {
            var key = {
                key: earlyAccess.userKey
            };
            xhr.sendRequest("post", "data=" + JSON.stringify(key), "server/register/early_access.php", earlyAccess.interpretCheckKey);
        } else {
            document.getElementById("key_correct").innerHTML = "";
        }
    },
    interpretCheckKey: function(response) {
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse.eak[0].legal === true) {
                document.getElementById("key_correct").className = "correct";
                document.getElementById("key_correct").innerHTML = "Key is valid";
                earlyAccess.keyValid = true;
            } else {
                document.getElementById("key_correct").className = "incorrect";
                document.getElementById("key_correct").innerHTML = "This is not a valid key";
                earlyAccess.keyValid = false;
            }
        }
    }
};