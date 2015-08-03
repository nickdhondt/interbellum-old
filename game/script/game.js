"use strict";

// Initiate the script when the page has been loaded
window.addEventListener("DOMContentLoaded", function(){initGame()});

function initGame() {
    // Nothing here yet
}

var map = map || {};

map = {
    coordinateSize: 80,
    coordinatesPerTile: 7,
    horizontal: 1,
    vertical: 1,
    mouseDown: false,
    previousX: false,
    previousY: false,
    generatingLeft: false,
    generatingRight: false,
    generatingTop: false,
    generatingBottom: false,
    wasMoving: false,
    validAction: false,
    cityTypes: ["own", "current", "fed", "ally", "enemy", "pact", "other"],
    mapCenterX: settings.map.centerX,
    mapCenterY: settings.map.centerY,
    mapSize: settings.map.size,
    mapInteractionBlocked: false,
    mapPositionReady: false,
    mapEventsInitialized: false,
    delayMapId: -1,
    initMap: function() {
        map.horizontal = map.calcHorizontalTiles();
        map.vertical = map.calcVerticalTiles();
        xhr.sendRequest("get", "", "../server/map/cities.php?x=" + map.mapCenterX + "&y=" + map.mapCenterY + "&h=" + map.horizontal + "&v=" + map.vertical, map.interpretGetCoordinates);
        if (map.mapEventsInitialized === false) document.getElementById("map").addEventListener("mouseup", function(){map.removeContextMenus()});
    },
    calcHorizontalTiles: function() {
        return Math.ceil((window.innerWidth + (map.coordinatesPerTile * map.coordinateSize)) / (map.coordinatesPerTile * map.coordinateSize) + 1);
    },
    calcVerticalTiles: function(){
        return Math.ceil((window.innerHeight + (map.coordinatesPerTile * map.coordinateSize)) / (map.coordinatesPerTile * map.coordinateSize) + 1);
    },
    interpretGetCoordinates: function(response){
        if (xhr.requestSuccessful(response)){
            var parsedResponse = support.parseJSON(response);

            if(parsedResponse !== null) {
                if (parsedResponse.logged_in === true) {
                    application.loading.initMap = true;
                    application.loadGame();
                    application.updateInitLoader();
                    var mapContainer = document.getElementById("map_container");
                    while(mapContainer.hasChildNodes()) mapContainer.removeChild(mapContainer.firstChild);
                    mapContainer.style.width = map.coordinatesPerTile * map.coordinateSize * map.horizontal + "px";
                    if (map.mapEventsInitialized === false) {
                        mapContainer.addEventListener("touchstart", function(){
                            if (map.mapInteractionBlocked === false) {
                                map.mouseDown = true;
                                map.previousX = false;
                                map.previousY = false;
                            }
                        });
                        mapContainer.addEventListener("mousedown", function(){
                            if (map.mapInteractionBlocked === false) {
                                map.mouseDown = true;
                                map.previousX = false;
                                map.previousY = false;
                            }
                        });
                        document.addEventListener("touchmove", function(e){map.moveMap(e)});
                        document.addEventListener("mousemove", function(e){map.moveMap(e)});
                        document.addEventListener("touchend", function(){
                            if (map.mapInteractionBlocked === false) {
                                map.mouseDown = false;
                                map.wasMoving = false
                            }
                        });
                        document.addEventListener("mouseup", function(){
                            if (map.mapInteractionBlocked === false) {
                                map.mouseDown = false;
                                map.wasMoving = false
                            }
                        });

                        map.mapEventsInitialized = true;
                    }

                    for (var i = 0; i < parsedResponse.feedback.length; i++) {
                        var coordinatesTileNode = map.generateTile(parsedResponse.feedback[i].x, parsedResponse.feedback[i].y, parsedResponse.feedback[i]);

                        mapContainer.appendChild(coordinatesTileNode);
                    }
                } else window.location.assign("../logged_out.php");
            }
        }

        map.mapInteractionBlocked = false;
    },
    generateTile: function(xCoor, yCoor, coordinateObjects) {
        if (typeof xCoor === "undefined" || typeof yCoor === "undefined") {
            xCoor = 0;
            yCoor = 0;
        }

        var coordinatesTileNode = document.createElement("div");
        coordinatesTileNode.x = xCoor;
        coordinatesTileNode.y = yCoor;

        for (var y = yCoor - Math.floor(map.coordinatesPerTile / 2); y <= yCoor + Math.floor(map.coordinatesPerTile / 2); y++) {
            var rowNode = document.createElement("div");

            for (var x = xCoor - Math.floor(map.coordinatesPerTile / 2); x <= xCoor + Math.floor(map.coordinatesPerTile / 2); x++) {
                var coordinateNode = document.createElement("div");
                if (Math.random() > 0.66) coordinateNode.setAttribute("class", "grass_one default");
                else if (Math.random() > 0.33) coordinateNode.setAttribute("class", "grass_two default");
                else coordinateNode.setAttribute("class", "grass_three default");

                for(var coordinateObject in coordinateObjects){
                    if (coordinateObjects.hasOwnProperty(coordinateObject)) {
                        if (typeof coordinateObjects[coordinateObject] === "object" && coordinateObjects[coordinateObject].x === x && coordinateObjects[coordinateObject].y === y) {
                            map.changeCoordinate(coordinateNode, coordinateObjects[coordinateObject]);
                            break;
                        }
                    }
                }

                if (x < 0 || y < 0 || x > map.mapSize || y > map.mapSize) {
                    var random = Math.random();
                    if (random > 0.66) {
                        coordinateNode.setAttribute("class", "empty_one");
                    } else if (random > 0.33) {
                        coordinateNode.setAttribute("class", "empty_two");
                    } else {
                        coordinateNode.setAttribute("class", "empty_three");
                    }
                } else {
                    var coordinateNumberNode = document.createElement("div");
                    var coordinateNumberTextNode = document.createTextNode("(" + x + ", " + y + ")");
                    coordinateNumberNode.setAttribute("class", "coordinate");
                    coordinateNumberNode.appendChild(coordinateNumberTextNode);
                    coordinateNode.appendChild(coordinateNumberNode);
                }

                rowNode.appendChild(coordinateNode);
            }

            coordinatesTileNode.appendChild(rowNode);
        }

        return coordinatesTileNode;
    },
    moveMap: function(e) {
        if (map.mouseDown === true && map.mapInteractionBlocked === false) {
            map.wasMoving = true;
            e.preventDefault();
            var x, y;

            if (typeof e.clientX === "undefined") x = e.touches[0].clientX;
            else x = e.clientX;
            if (typeof e.clientY === "undefined") y = e.touches[0].clientY;
            else y = e.clientY;

            var mapContainer = document.getElementById("map_container");

            if (map.previousX === false) map.previousX = x;
            if (map.previousY === false) map.previousY = y;

            var newLeft = mapContainer.offsetLeft - (map.previousX - x);
            var newTop = mapContainer.offsetTop - (map.previousY - y);

            mapContainer.style.left = newLeft + "px";
            mapContainer.style.top = newTop + "px";

            map.previousX = x;
            map.previousY = y;

            map.getNewTiles();
        }
    },
    placeTiles: function(response, tiles) {
        if (xhr.requestSuccessful(response)) {
            var parsedResponse = support.parseJSON(response);

            if (parsedResponse !== null) {
                if (parsedResponse.logged_in === true){
                    for (var tile in parsedResponse.feedback) {
                        if (parsedResponse.feedback.hasOwnProperty(tile)) {
                            for(var i = 0; i < tiles.length; i++){
                                if(tiles[i].x === parsedResponse.feedback[tile].x && parsedResponse.feedback[tile].x === tiles[i].x) {
                                    map.placeObjects(parsedResponse.feedback[tile], tiles[i]);
                                }
                            }
                        }
                    }
                } else window.length.assign("../logged_out.php");
            }
        }
    },
    placeObjects: function(objects, tile) {
        var offset = Math.floor(map.coordinatesPerTile / 2);
        for (var y = 0; y < tile.childNodes.length; y++) {
            var row = tile.childNodes[y];
            for (var x = 0; x < row.childNodes.length; x++) {
                var actualX = x + tile.x - offset;
                var actualY = y + tile.y - offset;

                for (var object in objects) {
                    if (objects.hasOwnProperty(object)) {
                        if (typeof objects[object] === "object") {
                            if (objects[object].x === actualX && objects[object].y === actualY) {
                                map.changeCoordinate(row.childNodes[x], objects[object]);
                                break;
                            }
                        }
                    }
                }
            }
        }
    },
    changeCoordinate: function(coordinate, object) {
        coordinate.city = object.city;
        coordinate.owner = object.owner;
        coordinate.optionsVisible = false;
        coordinate.setAttribute("class", "obj " + map.cityTypes[object.type]);
        coordinate.addEventListener("click", function(e){map.showContext(e)});
        coordinate.addEventListener("mouseenter", function(e){map.showDetails(e)});
        coordinate.addEventListener("mouseout", function(){map.removeDetailPanels()});
        var pointsNode = document.createElement("div");
        pointsNode.setAttribute("class", "points");
        var pointsTextNode = document.createTextNode(object.points);
        pointsNode.appendChild(pointsTextNode);
        coordinate.appendChild(pointsNode);
    },
    removeDetailPanels: function() {
        var contextNodes = document.getElementsByClassName("city_details");
        for(var i = 0; i < contextNodes.length; i++){
            contextNodes[i].parentNode.removeChild(contextNodes[i]);
        }
    },
    showDetails: function(e) {
        e.stopPropagation();

        var object = e.target;
        var detailsAlreadyAppended = false;

        if (!object.classList.contains("obj")) object = e.target.parentNode;

        for(var i = 0; i < object.childNodes.length; i++) {
            if (object.childNodes[i].className === "city_details") detailsAlreadyAppended = true;
        }

        if (object.optionsVisible === false && detailsAlreadyAppended === false) {
            var detailsContainerNode = document.createElement("div");
            detailsContainerNode.setAttribute("class", "city_details");
            // Todo: finish content
            var detailContainerTextNode = document.createTextNode("Owner: " + object.owner);
            detailsContainerNode.appendChild(detailContainerTextNode);

            object.appendChild(detailsContainerNode);
        }
    },
    removeContextMenus: function() {
        if (map.wasMoving === false) {
            var contextNodes = document.getElementsByClassName("object_context");
            for(var i = 0; i < contextNodes.length; i++){
                contextNodes[i].parentNode.optionsVisible = false;
                contextNodes[i].parentNode.removeChild(contextNodes[i]);
            }
        }
    },
    showContext: function(e) {
        e.stopPropagation();
        map.removeContextMenus();
        map.removeDetailPanels();

        var object = e.target;
        if (!object.classList.contains("obj")) object = e.target.parentNode;

        object.optionsVisible = true;

        // Todo: loop
        var contextNode = document.createElement("div");
        contextNode.setAttribute("class", "object_context");
        var optionOneNode = document.createElement("div");
        optionOneNode.setAttribute("class", "one option");
        var optionTwoNode = document.createElement("div");
        optionTwoNode.setAttribute("class", "two option");
        var optionThreeNode = document.createElement("div");
        optionThreeNode.setAttribute("class", "three option");
        var optionFourNode = document.createElement("div");
        optionFourNode.setAttribute("class", "four option");
        var optionFiveNode = document.createElement("div");
        optionFiveNode.setAttribute("class", "five option");
        var optionSixNode = document.createElement("div");
        optionSixNode.setAttribute("class", "six option");
        var optionDataNode = document.createElement("div");
        var optionDataTextNode = document.createTextNode(object.city);
        optionDataNode.appendChild(optionDataTextNode);
        optionOneNode.addEventListener("click", function(e){e.stopPropagation()});
        optionOneNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        optionTwoNode.addEventListener("click", function(e){e.stopPropagation()});
        optionTwoNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        optionThreeNode.addEventListener("click", function(e){e.stopPropagation()});
        optionThreeNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        optionFourNode.addEventListener("click", function(e){e.stopPropagation()});
        optionFourNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        optionFiveNode.addEventListener("click", function(e){e.stopPropagation()});
        optionFiveNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        optionSixNode.addEventListener("click", function(e){e.stopPropagation()});
        optionSixNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        optionDataNode.addEventListener("click", function(e){e.stopPropagation()});
        optionDataNode.addEventListener("mouseup", function(e){e.stopPropagation();map.mouseDown = false;map.wasMoving = false});
        contextNode.appendChild(optionOneNode);
        contextNode.appendChild(optionTwoNode);
        contextNode.appendChild(optionThreeNode);
        contextNode.appendChild(optionFourNode);
        contextNode.appendChild(optionFiveNode);
        contextNode.appendChild(optionSixNode);
        contextNode.appendChild(optionDataNode);
        object.appendChild(contextNode);
    },
    positionTiles: function(direction, mapContainer) {
        var newTiles = [];
        var i, j, integer, integerTwo;

        var currentTiles = mapContainer.children;
        var xTop = currentTiles[0].x;
        var yTop = currentTiles[0].y;

        switch (direction) {
            case 0:
                for(i = 0; i < map.horizontal; i++) {
                    newTiles.push(map.generateTile(xTop + (i * map.coordinatesPerTile), yTop - map.coordinatesPerTile));
                }

                xhr.sendRequest("get", "", "../server/map/cities.php?x=" + eval(xTop + Math.floor(((map.horizontal - 1) * map.coordinatesPerTile) / 2)) + "&y=" + eval(yTop - map.coordinatesPerTile) + "&h=" + map.horizontal, function(e){map.placeTiles(e, newTiles)});

                break;
            case 1:
                for(i = 0; i < map.vertical; i++) {
                    newTiles.push(map.generateTile(xTop + (map.horizontal * map.coordinatesPerTile), yTop + (i * map.coordinatesPerTile)));
                }

                xhr.sendRequest("get", "", "../server/map/cities.php?x=" + eval(xTop + (map.coordinatesPerTile * map.horizontal)) + "&y=" + eval(yTop + Math.floor(((map.vertical - 1) * map.coordinatesPerTile) / 2)) + "&v=" + map.vertical, function(e){map.placeTiles(e, newTiles)});

                break;
            case 2:
                for(i = 0; i < map.horizontal; i++) {
                    newTiles.push(map.generateTile(xTop + (i * map.coordinatesPerTile), yTop + (map.vertical * map.coordinatesPerTile)));
                }

                xhr.sendRequest("get", "", "../server/map/cities.php?x=" + eval(xTop + Math.floor(((map.horizontal - 1) * map.coordinatesPerTile) / 2)) + "&y=" + eval(yTop + (map.coordinatesPerTile * map.vertical)) + "&h=" + map.horizontal, function(e){map.placeTiles(e, newTiles)});

                break;
            case 3:
                for(i = 0; i < map.vertical; i++) {
                    newTiles.push(map.generateTile(xTop - map.coordinatesPerTile, yTop + (i * map.coordinatesPerTile)));
                }

                xhr.sendRequest("get", "", "../server/map/cities.php?x=" + eval(xTop - map.coordinatesPerTile) + "&y=" + eval(yTop + Math.floor(((map.vertical - 1) * map.coordinatesPerTile) / 2)) + "&v=" + map.vertical, function(e){map.placeTiles(e, newTiles)});

                break;
        }

        var tiles = [];

        switch (direction) {
            case 0:
                for(j = 1; j <= currentTiles.length; j++) {
                    if (j <= map.horizontal) {
                        tiles.push(newTiles[j - 1]);
                    }
                }

                for(j = 1; j <= currentTiles.length; j++) {
                    if (j <= currentTiles.length - map.horizontal) {
                        tiles.push(currentTiles[j - 1]);
                    }
                }
                break;
            case 1:
                for(j = 1; j <= currentTiles.length; j++) {
                    integer = j / map.horizontal;
                    integerTwo = (j - 1) / map.horizontal;

                    if (integerTwo !== parseInt(integerTwo)) {
                        tiles.push(currentTiles[j - 1]);
                    }

                    if (integer === parseInt(integer)) {
                        tiles.push(newTiles[integer - 1]);
                    }
                }
                break;
            case 2:
                for(j = 1; j <= currentTiles.length; j++) {
                    if (j > map.horizontal) {
                        tiles.push(currentTiles[j - 1]);
                    }
                }

                for(j = 1; j <= currentTiles.length; j++) {
                    if (j > currentTiles.length - map.horizontal) {
                        tiles.push(newTiles[j - 1 - (map.horizontal * (map.vertical - 1))]);
                    }
                }
                break;
            case 3:
                for(j = 1; j <= currentTiles.length; j++) {
                    integer = j / map.horizontal;
                    integerTwo = (j - 1) / map.horizontal;
                    if (integerTwo === parseInt(integerTwo)) {
                        tiles.push(newTiles[integerTwo]);
                    }

                    if (integer !== parseInt(integer)) {
                        tiles.push(currentTiles[j - 1]);
                    }
                }
                break;
        }

        while(mapContainer.hasChildNodes()) mapContainer.removeChild(mapContainer.firstChild);

        for(var k = 0; k < tiles.length; k++) {
            mapContainer.appendChild(tiles[k]);
        }

    },
    getNewTiles: function() {
        var mapContainer = document.getElementById("map_container");

        var left = mapContainer.offsetLeft;
        var top = mapContainer.offsetTop;

        if (left >= -150) {
            if (map.generatingLeft === false) {
                map.generatingLeft = true;

                map.positionTiles(3, mapContainer);

                mapContainer.style.left = mapContainer.offsetLeft - map.coordinatesPerTile * map.coordinateSize + "px";
                map.generatingLeft = false;
            }
        } else if (left <= -(mapContainer.clientWidth - window.innerWidth - 150)) {
            if (map.generatingRight === false) {
                map.generatingRight = true;

                map.positionTiles(1, mapContainer);

                mapContainer.style.left = mapContainer.offsetLeft + map.coordinatesPerTile * map.coordinateSize + "px";
                map.generatingRight = false;
            }
        }

        if (top >= -150) {
            if (map.generatingTop === false) {
                map.generatingTop = true;

                map.positionTiles(0, mapContainer);

                mapContainer.style.top = mapContainer.offsetTop - map.coordinatesPerTile * map.coordinateSize + "px";
                map.generatingTop = false;
            }
        } else if (top <= -(mapContainer.clientHeight - window.innerHeight - 150)) {
            if (map.generatingBottom === false) {
                map.generatingBottom = true;

                map.positionTiles(2, mapContainer);

                mapContainer.style.top = mapContainer.offsetTop + map.coordinatesPerTile * map.coordinateSize + "px";
                map.generatingBottom = false;
            }
        }
    },
    setMapPosition: function(left, top) {
        if (map.mapPositionReady === false) {
            map.mapPositionReady = true;

            var mapPage = document.getElementById("map");
            var mapContainer = document.getElementById("map_container");

            if (typeof left === "undefined") {
                if (map.horizontal % 2 === 0) mapContainer.style.left = Math.round((mapPage.clientWidth - mapContainer.clientWidth) / 2) + (map.coordinateSize / 2) + "px";
                else mapContainer.style.left = Math.round((mapPage.clientWidth - mapContainer.clientWidth) / 2) + "px";
            }

            if (typeof top === "undefined") {
                if (map.vertical % 2 === 0) mapContainer.style.top = Math.round((mapPage.clientHeight - mapContainer.clientHeight) / 2) + (map.coordinateSize / 2) + "px";
                else mapContainer.style.top = Math.round((mapPage.clientHeight - mapContainer.clientHeight) / 2) + "px";
            }
        }
    },
    reloadMap: function() {
        map.calcMapCenter();

        if (map.horizontal !== map.calcHorizontalTiles() || map.vertical !== map.calcVerticalTiles()) {
            map.mapInteractionBlocked = true;
            map.initMap();
        }
    },
    calcMapCenter: function() {
        // Todo: better centering

        var mapContainer = document.getElementById("map_container");
        var currentTiles = mapContainer.children;

        map.mapCenterX = currentTiles[0].x + Math.floor((map.coordinatesPerTile * (map.horizontal - 1)) / 2);
        map.mapCenterY = currentTiles[0].y + Math.floor((map.coordinatesPerTile * (map.vertical - 1)) / 2);
    }
};