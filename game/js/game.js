var mapSize = 20;
var mapWidth = 7;
var mapRadius = Math.floor(mapWidth / 2);

function getMapData(x,y) {
    busy = true;

    if (x < 0) {
        x = 0;
    }

    if (y < 0) {
        y = 0;
    }

    if (x > mapSize) {
        x = mapSize;
    }

    if (y > mapSize) {
        y = mapSize;
    }

    currentX = x;
    currentY = y;

    var pullMapData = new XMLHttpRequest();

    var loaderTimeout = setTimeout(showLoader, 100);

    pullMapData.onreadystatechange = function() {
        //console.log("State change");
        if (pullMapData.readyState == 4 && pullMapData.status == 200) {
            //console.log("Let's roll");

            var mapDataReceived = pullMapData.response;
            mapData = JSON.parse(mapDataReceived);

            generateMap(x, y, mapData);

            hideLoader();

            showMap();

            clearTimeout(loaderTimeout);
            busy = false;
        }
    };

    pullMapData.open("get", "includes/pull_map_data.php?x="+x+"&y="+y);
    pullMapData.send();
}

function showLoader() {
    var loader = document.getElementById("loader");
    var img = document.getElementById("loadimg");

    loader.style.opacity = 1;
    loader.style.pointerEvents = "auto";
    img.style.pointerEvents = "none";
}

function hideLoader() {
    var loader = document.getElementById("loader");

    loader.style.opacity = 0;
    loader.style.pointerEvents = "none";
}

function showMap() {
    var mapField = document.getElementById("buttons");

    mapField.style.opacity = 1;
}

function generateMap(x, y, mapData) {
    var citys = "";

    for (var j = (y - mapRadius); j <= (y + mapRadius); j++) {
        for (var i = (x - mapRadius); i <= (x + mapRadius); i++) {
            var statusFound = false;

            for(var p = 0; p < mapData.length; p++) {
                if (mapData[p].x == i && mapData[p].y == j && mapData[p].type == 0 && statusFound == false) {
                    citys += makeTile("owncity", i, j);
                    statusFound = true;
                } else if (mapData[p].x == i && mapData[p].y == j && mapData[p].type == 2 && statusFound == false) {
                    citys += makeTile("othercity", i, j);
                    statusFound = true;
                }
            }

            if (statusFound == false) {
                if (j < 0 || i < 0 || j > mapSize || i > mapSize) {
                    citys += makeTile();
                } else {
                    citys += makeTile("grass", i, j);
                }
            }
        }
    }

    placeMap(citys);
    if (typeof highlightX != "undefined" && typeof highlightY != "undefined" && tileInRange(highlightX, highlightY) == true) {
        highlightTile(highlightX, highlightY);
    }
}

function makeTile(cityClass, i, j) {
    var insideTile = "<span class=\"coord\">("+i+", "+j+")</span>";

    if (cityClass == "owncity" || cityClass == "othercity") {
        return "<div class=\"" + cityClass + "\" id=\"" + i + ";" + j + "\" onclick=\"showInfo(" + i + "," + j + ")\" ondblclick=\"getMapData("+i+","+j+")\"><div class=\"opacity\" id=\"op" + i + "," + j + "\">" + insideTile + "</div></div>";
    } else if (cityClass == "grass" || cityClass == "hill") {
        return "<div class=\"" + cityClass + "\" id=\"" + i + ";" + j + "\" ondblclick=\"getMapData("+i+","+j+")\">" + insideTile + "</div>";
    } else {
        return "<div class=\"range\"></div>";
    }
}

function placeMap(citys) {
    var land = document.getElementById("land");

    land.innerHTML = citys;
}

function goUp() {
    if (currentY > 0 && busy == false) {
        getMapData(currentX, currentY - 2);
    }
}

function goDown() {
    if (currentY < mapSize && busy == false) {
        getMapData(currentX, currentY + 2);
    }
}

function goLeft() {
    if (currentX > 0 && busy == false) {
        getMapData(currentX - 2, currentY);
    }
}

function goRight() {
    if (currentX < mapSize && busy == false) {
        getMapData(currentX + 2, currentY);
    }
}

function showInfo(x, y) {
    var informationPanel = document.getElementById("information");

    for(var i = 0; i < mapData.length; i++) {
        if (mapData[i].x == x && mapData[i].y == y) {
            var cityInformation = "<h1>" + mapData[i].cityname + "</h1>" +
                "<strong>Stad:</strong> <a href=\"#\">" + mapData[i].cityname + "</a><br />" +
                "<strong>Eigenaar:</strong> <a href=\"#\">" + mapData[i].username + "</a><br />" +
                "<span class=\"href\" onclick='getMapData(" + x + "," + y + ")'>Centreren op kaart</span>";

            informationPanel.innerHTML = cityInformation;

            highlightTile(x, y);
        }
    }
}

function highlightTile(x, y) {
    if (typeof highlightX != "undefined" && typeof highlightY != "undefined") {
        tileHighlighted = document.getElementById("op" + highlightX + "," + highlightY);

        if (tileHighlighted != null) {
            tileHighlighted.style.background = "rgba(185, 185, 185, 0)";
        }
    }

    highlightX = x;
    highlightY = y;

    tileHighlighted = document.getElementById("op" + x + "," + y);
    tileHighlighted.style.background = "rgba(185, 185, 185, 0.5)";
}

function tileInRange(x, y) {
    if (x < currentX - mapRadius || x > currentX + mapRadius || y < currentY - mapRadius || y > currentY + mapRadius) return false;
    return true;
}