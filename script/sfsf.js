/**
 * @description: Functions for the 'Explorers of the Galaxy page'
 * @author: <a href="mailto:esteban.denis.ed@gmail.com">Esteban Denis</a>
 * @version: 2015 may 14
 */

//Global variables
var sldInterval;    //Interval for the slideshow
var popupInterval;  //Interval for the spacecraft message
var subErrorName = "";  //Check for the name field
var subErrorMail = "";  //Check for the mail field
var number_of_articles = 3; //#articles showed in the article index page.

//Declare functions as variables in order for removeEventHandler to work.
var prvArt = function() {ns_eotg_articles.prevArticle();};
var nxtArt = function() {ns_eotg_articles.nextArticle();};
var prvArtGalaxy = function(){ns_eotg_galaxy.prevArticle();};
var nxtArtGalaxy = function(){ns_eotg_galaxy.nextArticle();};
var prvLaunch =  function() {ns_eotg_projects.prevLaunch();};
var nxtLaunch =  function() {ns_eotg_projects.nextLaunch();};
var reqSubsr  = function() {ns_eotg_subscribe.requestSubscription();};

//Set the namespace for the homepage functionality.
var ns_eotg_home = {

    homeSlider_toggle: function(){
        //Set the loop and define first element.
        var milliseconds = 3000;    //TICK EVERY 3 SECONDS.
        if(sldInterval){
            console.log("slideshow interval cleared");
            sldInterval = clearInterval(sldInterval);
        } else {
            console.log("slideshow interval started");
            sldInterval = setInterval(function() {
                ns_eotg_home.nextSlideshowImage();
            }, milliseconds);
        }
    },

    nextSlideshowImage: function(){
        //Loop trough all info to determine the item that must be set as main object.
        var links = document.getElementById('slideshow').getElementsByTagName('div')[0].getElementsByTagName('a');
        var element = ns_eotg_home.getActiveElement(links);

        //Loop to assign ID's.
        for(var i=1; i<=links.length; i++){
            //Set the links and Set the slideshow_buttons
            var e = links[i-1];
            var btn = document.getElementById('slideshow_orb' + (i-1));
            if((i-1) == element){
                var cls = "pos" + i;
                btn.src = "images/icons/active_orb.png";
            } else {
                var cls = "pos" + i + " small";
                btn.src = "images/icons/inactive_orb.png";
            }
            e.setAttribute('class', cls);

        }

        //Set the info
        var descriptions = document.getElementById('uicontrol_top').getElementsByTagName('p');
        ns_eotg_home.toggleHidden(descriptions, element);

        //Set the images
        var images = document.getElementById('slideshow').getElementsByTagName('div')[1].getElementsByTagName('img');
        ns_eotg_home.toggleHidden(images, element);
    },

    getActiveElement: function(links){
        var element = 0;
        for(var i=0; i<links.length; i++){
            var s = links[i];
            var attrib = s.getAttribute('class');

            if(attrib.indexOf('small') < 0){
                if((i+1) == links.length){
                    element = 0;
                } else {
                    element = i+1;
                }
            }
        }
        return element;
    },

    toggleHidden: function(parent_element, main_element){
        for(var i=0; i<parent_element.length; i++){
            var child = parent_element[i];
            if(i == main_element){
                child.style.opacity = 1;
            } else {
                if(child.style.opacity != 0){
                    child.style.opacity = 0;
                }
            }
        }
    },

    initElements: function(){
        //This function initializes this page. (Set slideshow nav buttons, preload images, ...)
        //Make all the controls in the Uicontrol_bottom.
        ns_eotg_home.makeUicontrol_bottomControls();

        //Attach events to the elements on the home page.
        document.getElementById('uicontrol_bottom').getElementsByTagName('img')[0].addEventListener('click', function(event){ns_eotg_home.start_stopClick(event)});
        var imgs = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('article');
        for(var i=0; i< imgs.length; i++){
            var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('article')[i].getElementsByTagName('img')[0];
            img.addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title)});
        }
        console.log('init executed');
    },

    makeUicontrol_bottomControls: function () {
        var images = document.getElementById('slideshow').getElementsByTagName('div')[1].getElementsByTagName('img');
        var div = document.getElementById('uicontrol_bottom').getElementsByTagName('div')[0];

        //Before filling, clear the div
        while(div.hasChildNodes()) div.removeChild(div.firstChild);

        //Fill the div
        for(var i=0; i < images.length; i++){
            var img = document.createElement('img');
            img.src = "images/icons/inactive_orb.png";
            if(i==0) img.src = "images/icons/active_orb.png";
            img.id = "slideshow_orb" + i;
            img.addEventListener('click', function(event){ns_eotg_home.orbClick(event);});
            div.appendChild(img);
        }

        //Set the position of the div.
        var width = div.getElementsByTagName('img')[0].width;
        var totalwidth = document.getElementById('uicontrol_bottom').offsetWidth;
        div.style.position = 'absolute';
        div.style.marginLeft = (totalwidth/2 - eval((eval(36 * images.length))/2)) + "px";

        var enlarge = document.getElementById('enlarge');
        enlarge.style.marginLeft = (totalwidth - 2*36 - 6) + "px"; //Set margin as width - lentgh - padding;
        enlarge.addEventListener('click', function(){ns_eotg_home.enlargeImg()});
    },

    orbClick: function(event){
        var requested = event.target.id.substring(13);

        //When clicked on an imagebutton:
        //first turn off the interval
        ns_eotg_home.homeSlider_toggle();

        //secondly, loop until the requested element is reached.
        do{
            //Go to the next element
            ns_eotg_home.nextSlideshowImage();

            //Get the current active element
            var links = document.getElementById('slideshow').getElementsByTagName('div')[0].getElementsByTagName('a');
            var element = ns_eotg_home.getActiveElement(links) -1;
            if(element < 0) element = links.length -1;
        } while(element != requested);

        //Last, turn the timer back on.
        ns_eotg_home.homeSlider_toggle();
    },

    start_stopClick: function(event){
        var id = event.target.id;

        //Change the icon
        if(id == "play"){
            //Set to the pauze button.
            event.target.src = "images/icons/pauze_button.png";
            event.target.id = "pauze";
        } else {
            //Set to the play button.
            event.target.src = "images/icons/play_button.png";
            event.target.id = "play";
        }

        //Toggle the animation
        ns_eotg_home.homeSlider_toggle();
    },

    enlargeImg: function(){
        //Get the active element
        var links = document.getElementById('slideshow').getElementsByTagName('div')[0].getElementsByTagName('a');
        var element = ns_eotg_home.getActiveElement(links) -1;
        if(element < 0) element = links.length -1;
        var descr = document.getElementById('uicontrol_top').getElementsByTagName('p')[element];

        //Get the needed elements
        var imgs = document.getElementsByClassName('sldImageContainer')[0].getElementsByTagName('img')[element];
        var image = imgs.src;
        var des = descr.innerHTML;

        //Execute the function
        ns_general.enlargeImg(image, des);
    }
};

var ns_eotg_articles = {
    initElements: function(){
        //Attach events to the elements on the page.
        document.getElementById('atop').addEventListener('click', prvArt);
        document.getElementById('abottom').addEventListener('click', nxtArt);
        document.getElementsByTagName('h1')[0].addEventListener('click', function() {window.location.href="../index.html";});

        var imgs = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');
        for(var i=0; i< imgs.length; i++){
            var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[i].getElementsByTagName('img')[0];
            img.addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title)});
        }

        //This function initializes this page. (Set image eventlisteners, preload images, ...)
        ns_eotg_articles.analyzeArticles();

        console.log('init executed');
    },

    analyzeArticles: function(){
        //Get the needed Elements
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Get the total of current available elements
        var count = ns_eotg_articles.getArticleCount();

        //Get the first active article
        var active = ns_eotg_articles.getFirstActiveArticle();

        //En-/Disable buttons based on current article layout.
        if(active <= 0) ns_eotg_articles.disableButton('atop');
        else ns_eotg_articles.enableButton('atop');
        if(active >= parseInt(count - number_of_articles)) ns_eotg_articles.disableButton('abottom');
        else ns_eotg_articles.enableButton('abottom');
    },

    getArticleCount: function(){
        //Get the needed elements
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Get the Allow articles in the page
        var allowed = ns_eotg_articles.getAllowedArticles();

        //Loop all articles and check for allowance
        var count = 0;
        for(var i=0; i< articles.length; i++){
            if(allowed.indexOf(i) > -1) count++;
        }

        return count;
    },

    getAllowedArticles: function(){
        var allowed = [];
        //Get the elements from the filter
        var orb = document.getElementById('sptype_orbitational');
        var int = document.getElementById('sptype_interstellar');
        var gop = document.getElementById('pr_gop');
        var hubble = document.getElementById('pr_hst');
        var other = document.getElementById('pr_other');
        var photo = document.getElementById('type_photo');
        var video = document.getElementById('type_video');
        var date = document.getElementById('sort_date');
        var popul = document.getElementById('sort_popul');
        var sc = document.getElementById('sort_sc');
        var dist = document.getElementById('sort_dist');

        //Get and loop all articles
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');
        for(var i=0; i<articles.length; i++){
            var push = true;
            var sp_type = articles[i].getElementsByClassName('tags')[0].getElementsByClassName('sptype')[0];
            var project = articles[i].getElementsByClassName('tags')[0].getElementsByClassName('sptype')[0];
            var type = articles[i].getElementsByClassName('tags')[0].getElementsByClassName('sptype')[0];

            //Check the sp_type
            if(sp_type != undefined){
                switch (sp_type.textContent){
                    case "Orbitational":
                        if(push) push = orb.checked;
                        break;
                    case "Interstellar":
                        if(push) push = int.checked;
                        break
                }
            }

            //Check the type
            if(type != undefined){
                switch (type.textContent){
                    case "Photo":
                        if(push) push = photo.checked;
                        break;
                    case "Video":
                        if(push) push = video.checked;
                        break;
                }
            }

            //Check project
            if(project != undefined){
                switch (project){
                    case "GOP":
                        if(push) push = gop.checked;
                        break;
                    case "Hubble":
                        if(push) push = hubble.checked;
                        break;
                    default:
                        if(push) push = other.checked;
                }
            }

            //Check if pushed
            if(push) allowed.push(i);
        }

        return allowed;
    },

    getFirstActiveArticle: function(){
        //Get the needed elements
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Loop trough the articles
        var flag = -1;
        for(var i=0; i< articles.length; i++)
            if(flag == -1)
                if(!(articles[i].hasAttribute('hidden'))) flag = i;

        if(flag == -1) return 0;
        return flag;
    },

    prevArticle: function(){
        //Get the active Element
        var active = ns_eotg_articles.getFirstActiveArticle();
        var prev = parseInt(active - 1);

        ns_eotg_articles.setArticles(prev);
    },

    nextArticle: function(){
        //Get the active Element
        var active = ns_eotg_articles.getFirstActiveArticle();
        var next = parseInt(active + 1);

        ns_eotg_articles.setArticles(next);
    },

    setArticles: function(next){
        //Get the Articles
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Get the list of allowed Articles
        var allowed = ns_eotg_articles.getAllowedArticles();

        //Loop the articles
        var flag = parseInt(number_of_articles - 1);
        for(var i=0; i< articles.length; i++){
            if(i<next) articles[i].setAttribute('hidden', 'hidden');
            if(i== next) articles[i].removeAttribute('hidden');
            if(i>next){
                if(flag != 0){
                    if(allowed.indexOf(i) > -1){
                        articles[i].removeAttribute('hidden');
                        flag--;
                    }
                }
                else articles[i].setAttribute('hidden', 'hidden');
            }
        }

        //Re-analyse the controls
        ns_eotg_articles.analyzeArticles();
    },

    enableButton: function(button_name){
        //Get the needed element
        var button = document.getElementById(button_name);

        //Enable the button
        if(button != undefined){
            button.className = "button";
            if(button_name == "atop"){
                button.removeEventListener('click', prvArt);
                button.addEventListener('click', prvArt);
            } else if (button_name == "abottom") {
                button.removeEventListener('click', nxtArt);
                button.addEventListener('click', nxtArt);
            }
        }

        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        if(button_name == "atop") a.innerHTML = "Previous Articles &#9650;";
        if(button_name == "abottom") a.innerHTML = "Next Articles &#9660;";
        a.style.fontSize = 20 + "px";
    },

    disableButton: function(button_name){
        var button = document.getElementById(button_name);

        if(button != undefined){
            button.className = "button disabled";
            if(button_name == "atop"){
                button.removeEventListener('click', prvArt);
            } else if (button_name == "abottom"){
                button.removeEventListener('click', nxtArt);
            }
        }

        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        a.innerHTML = ns_eotg_articles.getArticleCount() + " articles found based on current search filter";
        a.style.fontSize = 15 + "px";

        var placeholder = document.getElementById('placeholder');
        var scr_width = parseInt(window.innerWidth);
        if(scr_width <= 460){a.style.fontSize = 12 + "px"; }
    }
};

var ns_eotg_article = {
    initElements: function() {
        //Attach events to the elements on the page.
        document.getElementsByTagName('h1')[0].addEventListener('click', function() {window.location.href="../index.html";});
        var imgs = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');
        for (var i = 0; i < imgs.length; i++) {
            var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[i].getElementsByTagName('div')[0].getElementsByTagName('img')[0];
            img.addEventListener('click', function () {
                ns_general.enlargeImg(event.target.src, event.target.title)
            });
        }

        //Get the title of the document
        ns_eotg_article.getArticleTitle();

        console.log('init executed');
    },

    getArticleTitle: function(){
        //Get the needed elements
        var h4 = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[0].getElementsByTagName('div')[1].getElementsByTagName('h3')[0];
        var title = h4.textContent;

        //Set the title
        var header = document.getElementsByTagName('h1')[0];
        header.innerHTML = "Explorers Of The Galaxy - " + title;
    }
};

var ns_eotg_spacecrafts = {
    initElements: function(){
        //Get the needed elements
        var bg = document.getElementById('spc_bg');
        var placeholder = document.getElementById('placeholder');

        //Set the position
        var popup = document.getElementsByClassName('popup')[0];
        var img = document.getElementById("spc_bg");
        var height = parseInt(img.height);
        var position = parseInt(img.style.top);
        var width = parseInt(parseInt(popup.offsetWidth) / 2);
        var window = parseInt(parseInt(placeholder.offsetWidth) / 2);

        popup.style.position = "absolute";
        popup.style.top = parseInt(height + position) + "px";
        popup.style.left = parseInt(window - parseInt(width)) + "px";

        //Assign the eventlisteners
        bg.addEventListener('click', function(event){ns_eotg_spacecrafts.showMessage(event);})
        document.getElementsByTagName('h1')[0].addEventListener('click', function() {window.location.href="../index.html";});

        //Time for user interaction
        setTimeout(function(){ns_eotg_spacecrafts.showMessage(' ');}, 5000);

        console.log("init executed");
    },

    showMessage: function(event){
        //Get the needed variables
        var orbit = document.getElementById('area_orbit');
        var inter = document.getElementById('area_inter');
        var popup = document.getElementsByClassName('popup')[0];

        //Show the message
        popup.style.opacity = 1;
        orbit.style.opacity = 1;
        inter.style.opacity = 1;

        //Timeout the deletion
        popupInterval = setInterval(function(){ns_eotg_spacecrafts.deleteMessage();}, 2000);
        //Set the reminder
        if(event == " ") setTimeout(function(){ns_eotg_spacecrafts.showMessage();}, 10000);

    },

    deleteMessage: function(){
        //Get the needed variables
        var orbit = document.getElementById('area_orbit');
        var inter = document.getElementById('area_inter');
        var popup = document.getElementsByClassName('popup')[0];

        //Delete the interval
        popupInterval = clearInterval(popupInterval);

        //Make the message invisible
        popup.style.opacity = 0;
        orbit.style.opacity = 0;
        inter.style.opacity = 0;
    }
};

var ns_eotg_spacecraft_article = {
    initElements: function(){
        //Attach events to the elements on the page.
        document.getElementsByTagName('h1')[0].addEventListener('click', function() {window.location.href="../index.html";});
        var imgs = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');
        for(var k=0; k< imgs.length; k++){
            var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[k].getElementsByTagName('div')[0].getElementsByTagName('img')[0];
            img.addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title)});
        }

        //Get the (optional) querystring
        var option = -1;
        var query = window.location.search;
        var location = (query.indexOf("object=")) + 8;
        if(location > 8){
            option = query.substring(query.indexOf("?object=") + 8);
            option++;
        }

        //Attach events to the aside section.
        var div =  document.getElementsByTagName('aside')[0].getElementsByTagName('div');
        var count = 0;
        for(var j=0; j < div.length; j++){
            var links = document.getElementsByTagName('aside')[0].getElementsByTagName('div')[j].getElementsByTagName('p');
            for(var i=0; i < links.length; i++){
                links[i].id = "link" + count;
                links[i].addEventListener('click', function(event) {ns_eotg_galaxy.jumpToIndex(event);});
                count++;

                //Check for query
                if(option != -1){
                    if(count == option) links[i].click();
                }
            }
        }

        //Analyze the initial articles
        ns_eotg_galaxy.analyzeArticles();

        console.log('init executed');
    },

    doArticleAdaptationsInterstellar: function(active){
        var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[active].getElementsByTagName('div')[0].getElementsByTagName('img')[0];

        if(active == 4) img.style.top = -25 + "px";
        if((active == 2) ||(active == 8)) img.style.top = -40 + "px";
        if((active == 3) || (active == 7)) img.style.top = -70 + "px";
        if(active == 0) img.style.top = -100 + "px";
        if(active == 0) img.style.top = -130 + "px";
    },

    doArticleAdaptationsOrbitational: function(active){
        var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[active].getElementsByTagName('div')[0].getElementsByTagName('img')[0];

        if((active == 0) ||(active == 3) || (active == 5)) img.style.top = -25 + "px";
        if(active == 2) img.style.top = -80 + "px";
        if((active == 1) || (active == 6) || (active == 9)) img.style.top = -100 + "px";
        if((active == 4) ||(active == 8)) img.style.top = 35 + "px";
    },

    setButtonTitles: function(position){
        if(position == "prev"){
            return "&#9664; Previous - ";
        } else {
            return "Next Spacecraft - ";
        }
    },

    extraButtonFunctionalityInterstellar: function (button, button_name) {
        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        if(button_name == "atop"){
            a.innerHTML = "Cassini was the very first interstellar probe in space";
        } else if (button_name == "abottom"){
            a.innerHTML = "Europa clipper is the most futuristic project available.";
        }
        a.style.fontSize = 15 + "px";

        var placeholder = document.getElementById('placeholder');
        var scr_width = parseInt(window.innerWidth);
        if(scr_width <= 460){a.style.fontSize = 11 + "px"; }
    },

    extraButtonFunctionalityOrbitational: function(button, button_name){
        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        if(button_name == "atop"){
            a.innerHTML = "Chandra is the First launched spacecraft for the DAWN project";
        } else if (button_name == "abottom"){
            a.innerHTML = "The WISE is our last stop for the Orbitational Crafts.";
        }
        a.style.fontSize = 15 + "px";

        var placeholder = document.getElementById('placeholder');
        var scr_width = parseInt(window.innerWidth);
        if(scr_width <= 460){a.style.fontSize = 11 + "px"; }
    }
};

var ns_eotg_galaxy = {
    initElements: function(){
        //Attach events to the elements on the page.
        var imgs = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');
        for(var k=0; k< imgs.length; k++){
            var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[k].getElementsByTagName('div')[0].getElementsByTagName('img')[0];
            img.addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title)});
        }

        //Get the (optional) querystring
        var option = -1;
        var query = window.location.search;
        var location = (query.indexOf("object=")) + 8;
        if(location > 8){
            option = query.substring(query.indexOf("?object=") + 8);
            option++;
        }

        //Attach events to the aside section.
        var div =  document.getElementsByTagName('aside')[0].getElementsByTagName('div');
        var count = 0;
        for(var j=0; j < div.length; j++){
            var links = document.getElementsByTagName('aside')[0].getElementsByTagName('div')[j].getElementsByTagName('p');
            for(var i=0; i < links.length; i++){
                links[i].id = "link" + count;
                links[i].addEventListener('click', function(event) {ns_eotg_galaxy.jumpToIndex(event);});
                count++;

                //Check for query
                if(option != -1){
                    if(count == option) links[i].click();
                }
            }
        }

        //Analyze the initial articles
        ns_eotg_galaxy.analyzeArticles();

        console.log('init executed');
    },

    jumpToIndex: function(event){
        //Extract the index
        var id = event.target.id;
        var index = id.substr(4, parseInt(id.length - 4));

        if(index != ""){
            //Set the p to the rightous index
            ns_eotg_galaxy.setPelementAtIndex(index);

            //Determine the position of the requested index
            var active = ns_eotg_galaxy.getActiveArticle();
            var direction = "";
            var dist = 0;

            if(index < active){
                direction = "down";
                dist = active - index;
            }
            if(index > active){
                direction = "up";
                dist = index - active;
            }

            //Loop until the rightous index is achieved.
            switch (direction){
                case "down":
                    for(var k=0; k < dist; k++){
                        ns_eotg_galaxy.prevArticle();
                    }
                    break;
                case "up":
                    for(var l=0; l < dist; l++){
                        ns_eotg_galaxy.nextArticle();
                    }
                    break;
                default:
                    break;
            }
        }
    },

    analyzeArticles: function(){
        //Get the number of articles
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article').length;

        //Get the value of the current active article
        var active = ns_eotg_galaxy.getActiveArticle();

        //Set the action accordingly
        if(active <= 0){
            ns_eotg_galaxy.disableButton("atop");
        } else {
            ns_eotg_galaxy.enableButton("atop");
        }
        if(active >= articles - 1){
            ns_eotg_galaxy.disableButton("abottom");
        } else {
            ns_eotg_galaxy.enableButton("abottom");
        }

        //Make adaptations to some of the pictures
        //Get the active page
        var distinct_int = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('interstellar probes');
        var distinct_orb = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('orbitational crafts');
        var distinct_pro = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('launches');

        //Execute the function
        if(distinct_int != -1) ns_eotg_spacecraft_article.doArticleAdaptationsInterstellar(active);
        else if (distinct_orb != -1) ns_eotg_spacecraft_article.doArticleAdaptationsOrbitational(active);
        else if (distinct_pro != -1) ns_eotg_projects.doArticleAdaptations(active);
        else ns_eotg_galaxy.doArticleAdaptions(active);
    },

    doArticleAdaptions: function(active){
        var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[active].getElementsByTagName('div')[0].getElementsByTagName('img')[0];

        if(active == 7){
            img.style.top = -40 + "px";
        }
        if(active == 9){
            img.style.top = -100 + "px";
        }
        if(active == 10){
            img.style.top = -90 + "px";
        }
    },

    getActiveArticle: function(){
        //Get the needed elements
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Loop trough the elements to determine the active one.
        var active = -1;
        for(var i=0; i < articles.length; i++){
            var article = articles[i];
            if(article.style.opacity == 1){
                active = i;
                break;
            }
        }

        //Return the gathered active element
        return active;
    },

    nextArticle: function(){
        //Get the needed elements
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Get the next element
        var current = ns_eotg_galaxy.getActiveArticle();
        var next = eval(current + 1);
        var doublenext = eval(next + 1);

        var b_current = articles[current];
        var b_next = articles[next];

        //Set the opacity of both elements
        b_current.style.opacity = 0;
        b_next.style.opacity = 1;

        //Set the z-index of both elements
        b_current.style.zIndex = 1;
        b_next.style.zIndex = 11;

        //Gather the information to display
        //set the p element to the next element
        ns_eotg_galaxy.setPelementAtIndex(next);

        //Set the button titles
        //Determination
        var distinct = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('galaxy architecture');

        if(current >= 0){
            var current_title = articles[current].getElementsByTagName('div')[1].getElementsByTagName('h3')[0];
            var atop = document.getElementById('atop').getElementsByTagName('a')[0];
            //Manual adaptation
            if(current_title.textContent == "The Interstellar Boundrary Explorer (IBEX)") current_title.textContent = "IBEX";
            if(current_title.textContent == "International Space Station (ISS)") if(parseInt(window.innerWidth) <= 1200) current_title.textContent = "ISS";


            //Get the active page
            var distinct_int = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('interstellar probes');
            var distinct_orb = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('orbitational crafts');
            var distinct_pro = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('launches');

            //Execute the function
            if ((distinct_orb != -1) || (distinct_int != -1)) atop.innerHTML = ns_eotg_spacecraft_article.setButtonTitles("prev") + current_title.innerHTML;
            else if (distinct_pro != -1) atop.innerHTML = ns_eotg_projects.setButtonTitles("prev") + current_title.innerHTML;
            else atop.innerHTML = ns_eotg_galaxy.setButtonTitles("prev") + current_title.innerHTML;
        }
        if(doublenext <= articles.length -1){
            var next_title = articles[doublenext].getElementsByTagName('div')[1].getElementsByTagName('h3')[0];
            var abottom = document.getElementById('abottom').getElementsByTagName('a')[0];
            //Manual adaptation
            if(next_title.textContent == "The Interstellar Boundrary Explorer (IBEX)") next_title.textContent = "IBEX";
            if(next_title.textContent == "International Space Station (ISS)") if(parseInt(window.innerWidth) <= 1200) next_title.textContent = "ISS";


            //Get the active page
            var distinct_int = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('interstellar probes');
            var distinct_orb = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('orbitational crafts');
            var distinct_pro = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('launches');

            //Execute the function
            if ((distinct_orb != -1) || (distinct_int != -1)) abottom.innerHTML = ns_eotg_spacecraft_article.setButtonTitles("next") + next_title.innerHTML + " &#9654;";
            else if (distinct_pro != -1) abottom.innerHTML = ns_eotg_projects.setButtonTitles("next") + next_title.innerHTML + "&#9654;";
            else abottom.innerHTML = ns_eotg_galaxy.setButtonTitles("next") + next_title.innerHTML + " &#9654;";
        }

        //Re-check the validation
        ns_eotg_galaxy.analyzeArticles();
    },

    prevArticle: function(){
        //Get the needed elements
        var articles = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article');

        //Get the next element
        var current = ns_eotg_galaxy.getActiveArticle();
        var prev = eval(current - 1);
        var double_prev = eval(prev - 1);

        var b_current = articles[current];
        var b_prev = articles[prev];

        //Set the opacity of both elements
        b_current.style.opacity = 0;
        b_prev.style.opacity = 1;

        //Set the z-index of both elements
        b_current.style.zIndex = 1;
        b_prev.style.zIndex = 11;

        //Gather the information to display
        //set the p element to the next element
        ns_eotg_galaxy.setPelementAtIndex(prev);

        //Set the button titles
        //Determination
        var distinct = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('galaxy architecture');

        if(double_prev >= 0){
            var prev_title = articles[double_prev].getElementsByTagName('div')[1].getElementsByTagName('h3')[0];
            var atop = document.getElementById('atop').getElementsByTagName('a')[0];
            //Manual adaptation
            if(prev_title.textContent == "The Interstellar Boundrary Explorer (IBEX)") prev_title.textContent = "IBEX";
            if(prev_title.textContent == "International Space Station (ISS)") if(parseInt(window.innerWidth) <= 1200) prev_title.textContent = "ISS";

            //Get the active page
            var distinct_int = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('interstellar probes');
            var distinct_orb = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('orbitational crafts');
            var distinct_pro = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('launches');

            //Execute the function
            if ((distinct_orb != -1) || (distinct_int != -1)) atop.innerHTML = ns_eotg_spacecraft_article.setButtonTitles("prev") + prev_title.innerHTML;
            else if (distinct_pro != -1) atop.innerHTML = ns_eotg_projects.setButtonTitles("prev") + prev_title.innerHTML;
            else atop.innerHTML = ns_eotg_galaxy.setButtonTitles("prev") + prev_title.innerHTML;

        }
        if(current <= articles.length){
            var next_title = articles[current].getElementsByTagName('div')[1].getElementsByTagName('h3')[0];
            var abottom = document.getElementById('abottom').getElementsByTagName('a')[0];
            //Manual adaptation
            if(next_title.textContent == "The Interstellar Boundrary Explorer (IBEX)") next_title.textContent = "IBEX";
            if(next_title.textContent == "International Space Station (ISS)") if(parseInt(window.innerWidth) <= 1200) next_title.textContent = "ISS";

            //Get the active page
            var distinct_int = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('interstellar probes');
            var distinct_orb = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('orbitational crafts');
            var distinct_pro = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('launches');

            //Execute the function
            if ((distinct_orb != -1) || (distinct_int != -1))  abottom.innerHTML = ns_eotg_spacecraft_article.setButtonTitles("next") + next_title.innerHTML + " &#9654;";
            else if (distinct_pro != -1) abottom.innerHTML = ns_eotg_projects.setButtonTitles("next") + next_title.innerHTML + " &#9654;";
            else abottom.innerHTML = ns_eotg_galaxy.setButtonTitles("next") + next_title.innerHTML + " &#9654;";
        }

        //Recheck the validation
        ns_eotg_galaxy.analyzeArticles();
    },

    setButtonTitles: function(position){
        if(position == "prev"){
            return "&#9664; Previous - ";
        } else {
            return "Next Up - ";
        }
    },

    setPelementAtIndex: function(index){
        //Get all the DIV elements
        var div =  document.getElementsByTagName('main')[0].getElementsByTagName('aside')[0].getElementsByTagName('div');

        //Loop trough all the div's
        var count = 0;
        for(var j= 0; j < div.length; j++){
            //Get all the P elements
            var p = div[j].getElementsByTagName('p');

            //Loop and clean all the elements + setup the asked one
            for(var i = 0; i < p.length; i++){
                if(count == index){
                    p[i].className = "active";
                } else {
                    p[i].className = "";
                }
                count++;
            }
        }
    },

    enableButton: function(button_name){
        //Make the button active using the general namespace.
        //Get the needed element
        var button = document.getElementById(button_name);
        if(button != undefined){
            button.className = "button";
            if(button_name == "atop"){
                button.removeEventListener('click', prvArtGalaxy);
                button.addEventListener('click', prvArtGalaxy);
            } else if (button_name == "abottom") {
                button.removeEventListener('click', nxtArtGalaxy);
                button.addEventListener('click', nxtArtGalaxy);
            }
        }

        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        a.style.fontSize = 20 + "px"

        var placeholder = document.getElementById('placeholder');
        var scr_width = parseInt(window.innerWidth);
        if(scr_width <= 460){a.style.fontSize = 14 + "px"; }
    },

    disableButton: function(button_name) {
        //Disable the button using the general namespace.
        var button = document.getElementById(button_name);
        if(button != undefined){
            button.className = "button disabled";
            if(button_name == "atop"){
                button.removeEventListener('click', prvArtGalaxy);
            } else if (button_name == "abottom"){
                button.removeEventListener('click', nxtArtGalaxy);
            }
        }

        //Extra Functionality
        //Get the active page
        var test = document.getElementsByTagName('h1')[0];
        var distinct_int = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('interstellar probes');
        var distinct_orb = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('orbitational crafts');
        var distinct_pro = document.getElementsByTagName('h1')[0].textContent.toLowerCase().indexOf('launches');

        //Execute the function
        if(distinct_int != -1) ns_eotg_spacecraft_article.extraButtonFunctionalityInterstellar(button, button_name);
        else if (distinct_orb != -1) ns_eotg_spacecraft_article.extraButtonFunctionalityOrbitational(button, button_name);
        else if (distinct_pro != -1) ns_eotg_projects.extraButtonFunctionality(button, button_name);
        else ns_eotg_galaxy.extraButtonFunctionality(button, button_name);
    },

    extraButtonFunctionality: function (button, button_name) {
        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        if(button_name == "atop"){
            a.innerHTML = "The sun is the beginning of your astronomical yourney";
        } else if (button_name == "abottom"){
            a.innerHTML = "The Supercluster Void is the largest imaginable";
        }
        a.style.fontSize = 15 + "px";

        var placeholder = document.getElementById('placeholder');
        var scr_width = parseInt(window.innerWidth);
        if(scr_width <= 460){a.style.fontSize = 11 + "px"; }
    }
};

var ns_eotg_projects = {
    initElements: function(){
        //This function initializes this page. (Set slideshow nav buttons, preload images, ...)

        //Attach events to the elements on the home page.
        document.getElementById('arrow_up').addEventListener('click', prvLaunch);
        document.getElementById('arrow_down').addEventListener('click', nxtLaunch);

        //document.getElementsByClassName('sldImageContainer flip')[0].getElementsByTagName('img')[0].addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title);});
        var imgs = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('article');
        for(var i=0; i< imgs.length; i++){
            var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('article')[i].getElementsByTagName('img')[0];
            img.addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title)});
        }

        //Get the (optional) querystring
        var option = -1;
        var query = window.location.search;
        var location = (query.indexOf("object=")) + 8;
        if(location > 8){
            option = query.substring(query.indexOf("?object=") + 8);
            option++;
        }

        //Attach events to the aside section.
        var div =  document.getElementsByTagName('aside')[0].getElementsByTagName('div');
        var count = 0;
        for(var j=0; j < div.length; j++){
            var links = document.getElementsByTagName('aside')[0].getElementsByTagName('div')[j].getElementsByTagName('p');
            for(var i=0; i < links.length; i++){
                links[i].id = "link" + count;
                links[i].addEventListener('click', function(event) {ns_eotg_galaxy.jumpToIndex(event);});
                count++;

                //Check for query
                if(option != -1){
                    if(count == option) links[i].click();
                }
            }
        }

        //Analyze the initial articles
        ns_eotg_galaxy.analyzeArticles();

        console.log('init executed');
    },

    doArticleAdaptations: function(active){
        if((active == 0) || (active == 3)) ns_eotg_projects.doGOPAdapations();
        var img = document.getElementsByTagName('main')[0].getElementsByTagName('section')[0].getElementsByTagName('section')[1].getElementsByTagName('article')[active].getElementsByTagName('div')[0].getElementsByTagName('img')[0];

        if(active == 5) img.style.top = -50 + "px";
        if(active == 4) img.style.top = -75 + "px";
        if(active == 2) img.style.top = -140 + "px";
    },

    doGOPAdapations: function(){
        for(var i = 2; i <= 5; i++){
            //Set the style
            var div = document.getElementsByClassName('galaxy')[0].getElementsByTagName('article')[0].getElementsByTagName('div')[i];
            var img = div.getElementsByTagName('img')[0];
            if(i == 4) img.style.left = -185 + "px";
            if(i==5) img.style.top = -50 + "px";

            //Set the event
            img.addEventListener('click', function(event){ window.location = event.target.alt; });
            img.style.zIndex = 14;
        }
    },

    setButtonTitles: function(position){
        if(position == "prev"){
            return "&#9664; Previous - ";
        } else {
            return "Next - ";
        }
    },

    extraButtonFunctionality: function (button, button_name) {
        //Extra functionality
        var a = button.getElementsByTagName('a')[0];
        if(button_name == "atop"){
            a.innerHTML = "The GOP Project is the biggest project active";
        } else if (button_name == "abottom"){
            a.innerHTML = "GRACE is the last project - More Projects are coming soon";
        }
        a.style.fontSize = 15 + "px";

        var placeholder = document.getElementById('placeholder');
        var scr_width = parseInt(window.innerWidth);
        if(scr_width <= 460){a.style.fontSize = 11 + "px"; }
    },

    getFirstActiveElement: function(){
        //Get needed variables
        var articles = document.getElementsByClassName('launches')[0].getElementsByTagName('section')[0].getElementsByTagName('article');

        //Loop trough the articles
        var flag = -1;
        for(var i=0; i< articles.length; i++)
            if(flag == -1)
                if(!(articles[i].hasAttribute('hidden'))) flag = i;

        if(flag == -1) return 0;
        return flag;
    },

    prevLaunch: function(){
        //Get the active element
        var active = ns_eotg_projects.getFirstActiveElement();
        var next = parseInt(active - 1);

        //Set the articles
        ns_eotg_projects.setArticles(next);
    },

    nextLaunch: function(){
        //Get the active element
        var active = ns_eotg_projects.getFirstActiveElement();
        var next = active + 1;

        //Set the articles
        ns_eotg_projects.setArticles(next);
    },

    setArticles: function(next){
        //Get needed variables
        var articles = document.getElementsByClassName('launches')[0].getElementsByTagName('section')[0].getElementsByTagName('article');

        //Set the attributes so that the previous article is showing
        var flag = number_of_articles;
        for(var i=0; i< articles.length; i++){
            if(i<next) articles[i].setAttribute('hidden', 'hidden');
            if(i== next) articles[i].removeAttribute('hidden');
            if(i>next){
                if(flag != 0){
                    articles[i].removeAttribute('hidden');
                    flag--;
                }
                else articles[i].setAttribute('hidden', 'hidden');
            }
        }

        //Check articles so that no false requests are to be made
        ns_eotg_projects.checkArticles();
    },

    checkArticles: function(){
        //Get needed variables
        var articles = document.getElementsByClassName('launches')[0].getElementsByTagName('section')[0].getElementsByTagName('article');
        var up = document.getElementById('arrow_up');
        var down = document.getElementById('arrow_down');

        //Get the active element
        var active = ns_eotg_projects.getFirstActiveElement();

        //Hide/show the buttons
        if(active == 0){
            up.style.opacity = 0;
            up.style.cursor = 'default';
            up.removeEventListener('click', prvLaunch);
        }
        else{
            up.style.opacity = 1;
            up.addEventListener('click', prvLaunch);
            up.style.cursor = 'pointer';
        }

        if(active == parseInt(articles.length - parseInt(number_of_articles + 1))){
            down.style.opacity = 0;
            down.removeEventListener('click', nxtLaunch);
            down.style.cursor = 'default';
        }
        else{
            down.style.opacity = 1;
            down.addEventListener('click', nxtLaunch);
            down.style.cursor = 'pointer';
        }
    }
};

var ns_eotg_subscribe = {
    initElements: function(){
        //Attach events to the elements on the page.
        document.getElementById('txt_name').addEventListener('focusout', function (event){ns_eotg_subscribe.checkInput(event);});
        document.getElementById('txt_mail').addEventListener('focusout', function (event){ns_eotg_subscribe.checkInput(event);});

        //The IMG section is commented because triggering an event while 'clicking away (on this image)' is experienced as 'annoying'.
        /*var imgs = document.getElementsByClassName('sldImageContainer')[0].getElementsByTagName('img');
         for(var i=0; i< imgs.length; i++){
         var img = document.getElementsByClassName('sldImageContainer')[0].getElementsByTagName('img')[i];
         img.addEventListener('click', function() {ns_general.enlargeImg(event.target.src, event.target.title)});
         } */

        console.log('init executed');
    },

    requestSubscription: function(){
        var valid = ns_eotg_subscribe.validateSubscription();
        if(valid){
            //Switch the opacity
            ns_eotg_subscribe.switchOpacity();
        }
    },

    switchOpacity: function(){
        //Get the needed elements
        var h2 = document.getElementsByClassName('subscr')[0].getElementsByTagName('h2')[0];
        var btn = document.getElementById('btn_home');
        var sub_box = document.getElementById('subscribe_box');

        //Change the opacity
        if(h2.style.opacity == 1){
            h2.style.opacity = 0;
            h2.style.zIndex = -3;
            btn.style.opacity = 0;
            btn.style.zIndex = -3;
            sub_box.style.opacity = 1;
        } else {
            h2.style.opacity = 1;
            btn.style.opacity = 1;
            sub_box.style.opacity = 0;
            h2.style.zIndex = 18;
            btn.style.zIndex = 20;
        }
    },

    validateSubscription: function(){
        //This function will check if everything is ok, and return it's findings.

        //Get the needed elements
        var subError = "";
        var name = document.getElementById('txt_name');
        var mail = document.getElementById('txt_mail');
        var opt1 = document.getElementById('opt1');
        var opt2 = document.getElementById('opt2');
        var opt3 = document.getElementById('opt3');
        var opt4 = document.getElementById('opt4');

        //Check the name and mail
        //If they got content, they are correct due to the other function that checks while typing.
        if(!((name.value.length > 1) && (mail.value.length > 5))){
            if((name.value.length <= 0) || (mail.value.length <= 0)) {
                ns_eotg_subscribe.pushMessageToButton("subscribe", false);
                return false;
            } else {
                subError = "Please fill in the whole form.";
            }
        } else {
            //Check the options
            if(!((opt1.checked) || (opt2.checked) || (opt3.checked) ||(opt4.checked))){
                subError = "Check at least one option";
            }
        }

        //Check if the inputboxes are clear
        if((subErrorMail == "") && (subErrorName == "")){
            //Enable the button if everything went OK.
            if(subError.length == 0){
                ns_eotg_subscribe.pushMessageToButton("subscribe", false);
                return true;
            } else {
                ns_eotg_subscribe.pushMessageToButton(subError, true);
                return false;
            }
        }
    },

    checkInput: function(event){
        //This function will check the two inputs and display the error if needed.

        //Set the flag
        var flag = false;
        var flagdata = "";

        //Get the event data
        var input = event.target.id;
        var element = document.getElementById(input);

        //Delete any previous error
        var existing_err = document.getElementById('error_' + input);
        if(existing_err != undefined){
            existing_err.remove();
        }

        //Check for event
        switch (input){
            case "txt_name":
                //The name must be at least 2 characters long
                if(element.value.length > 1){
                    //The name must contain a space.
                    var txt = element.value;
                    if(txt.indexOf(' ') == -1) flagdata = "Please fill in your name and surname."
                } else flagdata = "A one letter name? For real?";

                if(flagdata != "") subErrorName = flagdata;
                else subErrorName = "";
                break;
            case "txt_mail":
                //The mailadress must at least be 6 characters long
                if(element.value.length > 6){
                    //The mail adress must fulfill the following expression
                    //This is the simplefied expression from the RFC 2822 standard.
                    var pattern = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/;
                    flag = pattern.test(element.value);
                    if(flag == false) flagdata = "Please use a valid e-mail address.";
                } else flagdata = "An Email under 6 chars? For real?";

                if(flagdata != "") subErrorMail = flagdata;
                else subErrorMail = "";
                break;
            default:
                console.log("unexpected ID encountered. Please check the eventhandlers for the subscribefunction (init)  " + input);
                break;
        }

        //Pass the error to the user, if needed.
        if((flag == false) && (subErrorMail != "")) {
            ns_eotg_subscribe.pushMessageToButton(subErrorMail, true);
        } else {
            if(subErrorName != "") ns_eotg_subscribe.pushMessageToButton(subErrorName, true);
            else ns_eotg_subscribe.validateSubscription();
        }
    },

    pushMessageToButton: function(message, error){
        if(error){
            var a = document.getElementById('subscribe').getElementsByTagName('a')[0];
            a.innerHTML = message;
            a.style.color = "red";
            a.style.fontSize = 13 + "px";
            ns_eotg_subscribe.disableButton();
        } else {
            var a = document.getElementById('subscribe').getElementsByTagName('a')[0];
            a.innerHTML = message;
            a.style.color = "white";
            a.style.fontSize = 20 + "px";
            ns_eotg_subscribe.enableButton();
        }
    },

    enableButton: function(){
        //Get the button
        var btn = document.getElementById('subscribe');

        //Enable the button.
        btn.removeEventListener('click', reqSubsr);
        btn.addEventListener('click', reqSubsr);
        btn.className = "button";
    },

    disableButton: function(){
        //Get the button
        var btn = document.getElementById('subscribe');

        //Disable the button.
        btn.removeEventListener('click', reqSubsr);
        btn.className = "button disabled";
    }
};

var ns_general = {
    enlargeImg: function(image_source, image_description){
        //Get the needed elements
        var body = document.getElementsByTagName('body')[0];
        var descr = image_description;

        //Preload image
        var img = new Image();
        img.src = image_source;
        console.log(img.src);

        //Create the needed elements
        var div = document.createElement('div');
        var bg = document.createElement('div');
        var p = document.createElement('p');

        //Set the values of the element
        var source = img.src;
        div.addEventListener('click', function(source){ ns_general.shrinkImg(source)});
        p.text = descr;

        //Assign the created elements to the div
        body.appendChild(div);
        div.appendChild(bg);
        div.appendChild(img);
        div.appendChild(p);

        //Calculate the positioning of the elements
        div.className = "enlarge";
        div.style.width = window.innerWidth;
        div.style.height = window.innerHeight;
        div.style.marginTop = window.scrollY + 'px';
        var margin = eval(window.innerWidth /100 * 20);
        img.width = window.innerWidth - margin;

        //Lock the scrolling
        body.className = "disable_scroll";
    },

    shrinkImg: function(){
        //Get the needed elements
        var enlarged_div = document.getElementsByClassName('enlarge')[0];

        //Clear the children
        while(enlarged_div.hasChildNodes()){
            enlarged_div.removeChild(enlarged_div.childNodes[0]);
        }

        //Clear the element itself.
        enlarged_div.parentNode.removeChild(enlarged_div);

        //Reactivate the scrolling
        var body = document.getElementsByTagName('body')[0];
        body.className = "";
    },

    triggerChange: function(){
        //Get the needed elements
        var h1 = document.getElementsByTagName('h1')[0].textContent.toLowerCase();

        //Trigger the change
        if(h1.indexOf('articles') > -1){
            ns_eotg_articles.initElements();
            ns_eotg_articles.nextArticle();
            ns_eotg_articles.prevArticle();
        }
        else if (h1.indexOf('projects') > -1){
            ns_eotg_projects.initElements();
            ns_eotg_projects.nextLaunch();
            ns_eotg_projects.prevLaunch();
        }
        else if(h1.indexOf('home') > -1){ ns_eotg_home.initElements();}
        else if (h1.indexOf('spacecrafts') > -1) ns_eotg_spacecrafts.initElements();
        else if ((h1.indexOf('crafts') > -1) || (h1.indexOf('probes') > -1))ns_eotg_spacecraft_article.initElements();
        else if (h1.indexOf('galaxy architecture') > -1) ns_eotg_galaxy.initElements();
        else if (h1.indexOf('subscribe') > -1) ns_eotg_subscribe.initElements();
        else ns_eotg_article.initElements();

    },

    displayTiltMessage: function(){
        var tilt = document.getElementById('tilt_message');
        tilt.style.opacity = 1;
        var tmr_m = setTimeout(function (tmr_m) {ns_general.hideTiltMessage(tmr_m);}, 5000);
    },

    hideTiltMessage: function(tmr){
        clearTimeout(tmr);

        var tilt = document.getElementById('tilt_message');
        tilt.style.opacity = 0;
        tilt.zIndex = -1000;
    },

    setFooter: function(){
        //get the needed elements
        var scr_height = parseInt(window.innerHeight);
        var footer = document.getElementsByTagName('footer')[0];
        var footer_top = parseInt(footer.offsetTop);

        //Set the class.
        if(footer_top < scr_height)
        {
            footer.setAttribute("class", "mobile");

            //Display a message
            var body = document.getElementById('placeholder');
            var message = document.createElement('p');
            message.innerHTML = "Show footer &#9660;";
            message.style.color = "#1F659D";
            message.style.opacity = 0.8;
            message.style.marginTop = -10 + "px";
            message.style.cursor = "pointer";
            message.id = "message";
            message.addEventListener('click', function(){ns_general.forceDisplayFooter();});
            body.appendChild(message);
        }
        else footer.setAttribute("class", "");
    },

    forceDisplayFooter: function(){
        var footer =  document.getElementsByTagName('footer')[0];
        var message = document.getElementById('message');
        message.parentNode.removeChild(message);
        footer.setAttribute("class", "");
    },

    makeCurrentLink: function () {
        //Get the needed elements
        var h1 = document.getElementsByTagName('h1')[0].textContent.toLowerCase();

        //Set the title
        var nav = document.getElementsByTagName('nav');
        for(var i=0; i<nav.length; i++){
            var ul = nav[i].getElementsByTagName('ul')[0];
            if(h1.indexOf('home') > -1) {ul.getElementsByTagName('li')[0].getElementsByTagName('a')[0].style.color = "#7DB2FF" }
            if(h1.indexOf('articles') > -1) {ul.getElementsByTagName('li')[1].getElementsByTagName('a')[0].style.color = "#7DB2FF" }
            if(h1.indexOf('spacecrafts') > -1) {ul.getElementsByTagName('li')[2].getElementsByTagName('a')[0].style.color = "#7DB2FF" }
            if(h1.indexOf('architecture') > -1) {ul.getElementsByTagName('li')[3].getElementsByTagName('a')[0].style.color = "#7DB2FF" }
            if(h1.indexOf('projects') > -1) {ul.getElementsByTagName('li')[4].getElementsByTagName('a')[0].style.color = "#7DB2FF" }
            if(h1.indexOf('subscribe') > -1) {ul.getElementsByTagName('li')[5].getElementsByTagName('a')[0].style.color = "#7DB2FF" }
        }
    },

    autoScrollHeader: function(){
        //Get the needed elements
        var h1 = document.getElementsByTagName('h1')[0].textContent.toLowerCase();
        var header = document.getElementsByTagName('header')[0];
        var height = parseInt(header.offsetHeight);

        //Execute the auto scroll
        if(h1.indexOf('home') == -1) ns_general.scrollDown(0, height);
    },

    scrollDown: function(height, Goal){
        if(height <= Goal){
            setTimeout(function() {
                window.scrollTo(0, height);
                ns_general.scrollDown(height + 2, Goal);
            }, 5);
        }
    }
};

function initMainElements(){
    //This function will initialize the elements that go cross-page.
    var btnsearch = document.getElementById('btnsearch');
    var txtsearch = document.getElementById('txtsearch');
    var body = document.getElementsByTagName('body')[0];

    //Set globals based on screen width
    var scr_width = parseInt(window.innerWidth);
    if(scr_width <= 480){
        //Reset the number of articles
        number_of_articles = 2;

        //Trigger the changes made
        ns_general.triggerChange();
    }
    if(scr_width <= 640){ ns_general.displayTiltMessage(); }

    //Additional functions
    //Set the footer if needed
    ns_general.setFooter();
    //Color the current visited page on the navigation
    ns_general.makeCurrentLink();
    //If on mobile, autoscroll the header
    if(scr_width <= 480) ns_general.autoScrollHeader();

    //Set the handlers
    txtsearch.addEventListener('click', function() {txtsearch.value = "";});
    btnsearch.addEventListener('click', function() {searchRedirect();});
    document.getElementsByTagName('h1')[0].addEventListener('click', function() {window.location.href="index.html";});
    body.onresize = function() { ns_general.triggerChange(); };
}

function searchRedirect(){
    var txtsearch = document.getElementById('txtsearch');
    var value = txtsearch.value;

    if(value.length >= 1){
        if(value != "Search On Google"){
            var searchpath = value.replace(" ", "%20");
            //window.location.href = "http://www.google.com?q=" + searchpath;
            window.open("http://www.google.com?q=" + searchpath);
        }
    }
}