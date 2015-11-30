/**
 * Created by Shiner on 20/01/2015.
 */

$(document).ready(function () {
    console.log("JQuery started");
    var decoded, cleancodes, logintries = -1, connectInfo = [], recurCount = 0, searchInterval, autoInterval;

    setup();

    function setup() {
        console.log("startup started");
        $("#connectLog").append("<p>Setup Started.</p>");
    }

    // Connection function to retrieve Nucleus ID, Session ID and Phishing Token
    function connect() {
        logintries++;
        console.log("Connection started");
        $("#connectLog").append("<p>Connection Started. (" + cleancodes[logintries] + ")</p>");
        $('#connectButton').html("Connecting...");
        $.ajax({
            type: "POST",
            url: "php/ui.php",
            data: {backupcode: cleancodes[logintries]},
            success: function (result) {
                if (result.substring(0, 1) == "l") {
                    failedmessage = document.createElement('p');
                    $(failedmessage).html("Login Failed. No Fresh Backup Code Available.")
                        .css("color", "red");
                    $("#connectLog").append(failedmessage);
                    $("#connectButton").html("Reconnect");
                    deleteCode();
                } else {
                    $("#connectionStatus").html("<img src='img/green_light.png' alt='Green Light' height='20' width='20' /> Connected");
                    $("#connectLog").append("<p> Server Reply: " + result + "</p>");
                    connectInfo = JSON.parse(result);
                    var successmessage = document.createElement('p');
                    successmessage = document.createElement('p');
                    $(successmessage).html("Connection Successful.")
                        .css("color", "green");
                    $("#connectLog").append(successmessage);
                    $("#nucID").append(connectInfo['nucleusId']);
                    $("#sessID").append(connectInfo['sessionId']);
                    $("#phishTn").append(connectInfo['phishingToken']);
                    $("#connectButton").html("Reconnect");
                    deleteCode();
                    getCoins();
                }
            }
        })
    }

    function ajaxFunction(functionID) {
        data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: functionID
        };

        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                $("#coins").html(result);
                $("#updateCoins").html("Update");
            }
        });
    }

    function startTrading() {
        data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: "startTrade"
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {

            }
        });
    }

    function getCoins() {
        $("#updateCoins").html("Updating...");
        var coins = ajaxFunction("getCoins");
    }

    function deleteCode() {
        cleancodes.splice(0, 1);
        $('#codesleft').html("Backup Codes Left: " + cleancodes.length);
    }

    function playerSearch() {
        var nucleusId = connectInfo['nucleusId'];
        var sessionId = connectInfo['sessionId'];
        var phishingToken = connectInfo['phishingToken'];
        var playerId = $('#searchId').val();
        var data = {
            nucleusId: nucleusId,
            sessionId: sessionId,
            phishingToken: phishingToken,
            funId: "playerSearch",
            playerId: playerId
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                $("#stage").html(result);
            }
        });
    }

    function extractCodes() {
        var dirtycodes = $("#codeField").val();
        var codearray = dirtycodes.split(" ");
        codearray.splice(0, 1);
        for (var i = 0; i < codearray.length; i++) {
            cleancodes.push(codearray[i]);
            cleancodes[i] = codearray[i].substring(0, 8);
        }
        $("#codeconfirm").html("Codes Added Successfully.");
        $('#codesleft').html("Backup Codes Left: " + cleancodes.length);
    }

    function recursiveSearch(playerId, maxPrice) {
        console.log("Recursive Search Launched.");
        var nucleusId = connectInfo['nucleusId'];
        var sessionId = connectInfo['sessionId'];
        var phishingToken = connectInfo['phishingToken'];
        var data = {
            nucleusId: nucleusId,
            sessionId: sessionId,
            phishingToken: phishingToken,
            funId: "playerSearch",
            playerId: playerId,
            maxPrice: maxPrice
        };
        if (parseInt($('#coins').val()) < maxPrice){
            $('#stage').append("Not enough money");
        } else {
            $.ajax({
                type: "POST",
                url: "php/function.php",
                data: data,
                success: function (result) {
                    recursiveBuy(result);
                }
            });
        }
    }

    function recursiveBuy(result) {
        recurCount = recurCount + 1;
        $('#searchCount').html("Function run: " + recurCount + ". ");
        if (result.substring(0, 2) == '"n') {
            recurCount++;
            $('#stage').html("not found");
        } else if (result.substring(0, 1) == "No") {
            $('#connectLog').append("result");
        } else {
            $('#stage').html("Found these results!: " + result);
            getCoins();
        }
    }


    $("#connectButton").click(function () {
        connect()
    });
    $("#updateCoins").click(function () {
        getCoins();
    });

    function searchCaller(buyandSell) {
        var playerId = $('#searchId').val();
        var maxPrice = $('#maxprice').val();

        auto();

        if (buyandSell) {
            searchInterval = setInterval(function () {
                recursiveSearch(playerId, maxPrice);
                recursiveSearch(playerId, maxPrice);
            }, 60000);
        }
        autoInterval = setInterval(function() {
            auto();
        }, 300000);
    }

    $("#BuySellButton").click(function () {
        searchCaller(true);
    });

    $("#sellButton").click(function () {
        searchCaller(false);
    });

    $("#connectBox").hide();
    $("#backupcodes").hide();
    $("#codeshow").click(function (event) {
        $("#backupcodes").slideToggle();
        $("#codeshow").html("Click to hide connection box.");
    });
    $("#connectshow").click(function (event) {
        $("#connectBox").animate({
            height: 'toggle'
        });
    });

    $('#submitcodes').click(function () {
        cleancodes = [];
        extractCodes();
    });


    $('#stopRecr').click(function () {
        clearInterval(searchInterval);
        clearInterval(autoInterval)
    });

    $('#tradeButton').click(function () {
        var data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: "getTradePile"
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                $('#tradeDiv').html(result);
            }
        });
    });

    $('#watchButton').click(function () {
        var data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: "watchlist"
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                $('#watchDiv').html(result);
            }
        });
    });

    $('#marketButton').click(function () {
        var data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: "marketlist",
            playerID: 168542,
            maxPrice: 4400
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                //console.log(JSON.parse(result));
                $('#marketDiv').html(result);
            }
        });
    });

    function auto() {
        console.log("Auto function launched.");
        var addBin = $('#autoBin').val();
        var addStart = $('#autoStart').val();
        var data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: "autolist",
            addBin: addBin,
            addStart: addStart
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                console.log(result);
                decoded = JSON.parse(result);

                $('#service').html("<p>Currently Trading: <span id='active'> " + decoded['tradepile']['active'] + "</span></p>");
                $('#service').append("<p><span id='itemsTitle'>items: </span><br />");
                for (var i = 0; i < decoded['forsale'].length; i++) {
                    $('#service').append("<span class='info'> " + (i + 1) + "." + " Current: " +
                    decoded['forsale'][i]['currentBid'] + " Start: " +
                    decoded['forsale'][i]['startingBid'] + " Buy: " +
                    decoded['forsale'][i]['buyNowPrice'] + " expire: ~" +
                    decoded['forsale'][i]['expire'] + "</span><br />");
                }

                $('#service').append("<p>Total Sold: <span id='sold'>" + decoded['tradepile']['sold'] + "</span></p>");
                if (decoded['watchlist'] == "Nothing in watchlist.") {
                    $('#service').append("<p id='emptypile'>Nothing in watchlist.</p>");
                } else {
                    $('#service').append("<p id='emptypile'>ID's currently in wishlist: ");
                    for (var i; i < decoded['watchlist'].length; i++) {
                        $('#service').append(decoded['watchlist'][i] + " ");
                    }
                }
                if (decoded['tradepile'] == "Nothing in Trade Pile.") {
                    $('#service').append("<p id='emptypile'>Nothing in trade pile.</p>");
                }
                //$('#service').html(result);
            }
        });
    }

    $('#auctionButton').click(function () {
        var data = {
            nucleusId: connectInfo['nucleusId'],
            sessionId: connectInfo['sessionId'],
            phishingToken: connectInfo['phishingToken'],
            funId: "auction",
            id: $('#auctionInput').val()
        };
        $.ajax({
            type: "POST",
            url: "php/function.php",
            data: data,
            success: function (result) {
                $('#auctionDiv').html("<img src='"+result+"' />");
            }
        });
    });

});