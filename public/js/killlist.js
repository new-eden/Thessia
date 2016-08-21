/*
 * The MIT License (MIT)
 *
 * Copyright (c) 2016. Michael Karbowiak
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

var generateKillList = function(url) {
    var maxKillID = 0;
    var highestKillID = function(newID) {
        maxKillID = Math.max(maxKillID, newID);
        return maxKillID;
    };

    var generateKillList = function(kill) {
        var trHTML = "";

        // Convert the killTime from MongoISODateUTC thing to an ISOString for timeAgo
        // Stitch together the html that we want to output..
        trHTML += "" +
            "<tr onclick=\"window.location='/kill/" + kill.killID + "/'\">" +
            "<th class='hidden-sm-down' scope='row' data-toggle='tooltip' data-placement='right' title='" + new Date(kill.killTime).toString() + "'>" +
            "<span data-livestamp='" + kill.killTime.toString() + "'></span>" +
            "<br>" + millionBillion(kill.totalValue) +
            "</th>" +
            "<td class='hidden-md-down'>" +
            "<img data-toggle='tooltip' data-placement='right' title='" + kill.victim.shipTypeName + "' class='img-circle' src='https://imageserver.eveonline.com/Type/" + kill.victim.shipTypeID + "_64.png'/>" +
            "</td>" +
            "<td>" +
            "<a href='/solarsystem/" + kill.solarSystemID + "/'>" + kill.solarSystemName + "</a>" +
            "<br>" +
            "<a href='/region/" + kill.regionID + "'>" + truncate(kill.regionName, 15) + "</a>" +
            "</td>" +
            "<td class='hidden-md-down'>" +
            "<img data-toggle='tooltip' data-placement='right' title='" + kill.victim.characterName + "' class='img-circle' src='https://imageserver.eveonline.com/Character/" + kill.victim.characterID + "_64.jpg'/>" +
            "</td>" +
            "<td>" +
            "<a href='/character/" + kill.victim.characterID + "/'>" + kill.victim.characterName + " (<a href='/ship/" + kill.victim.shipTypeID + "/'>" + kill.victim.shipTypeName + "</a>)</a>" +
            "<br>" +
            "<a href='/corporation/" + kill.victim.corporationID + "/'>" + truncate(kill.victim.corporationName, 20) + "</a>";
        if (kill.victim.allianceID > 0) {
            trHTML += "/ <a href='/alliance/" + kill.victim.allianceID + "/'>" + truncate(kill.victim.allianceName, 20) + "</a>";
        }

        trHTML +=
            "</td>";

        // Figure how many attackers are in the attackers array
        var attackerCount = kill.attackers.length;

        // Loop over the attackers array, to find the finalBlow attacker, so we can finish this killlist element
        kill.attackers.forEach(function (attacker) {
            if (attacker.finalBlow == 1) {
                trHTML +=
                    "<td class='hidden-md-down'>" +
                    "<img data-toggle='tooltip' data-placement='right' title='" + attacker.characterName + "' class='img-circle' src='https://imageserver.eveonline.com/Corporation/" + attacker.corporationID + "_64.png'/>" +
                    "</td>" +
                    "<td>" +
                    "<a href='/character/" + attacker.characterID + "/'>" + attacker.characterName + " (" + attackerCount + ")</a><br><a href='/corporation/" + attacker.corporationID + "/'>" + truncate(attacker.corporationName, 20) + "</a>";
                if (attacker.allianceID > 0) {
                    trHTML += "/ <a href='/alliance/" + attacker.allianceID + "/'>" + truncate(attacker.allianceName, 20) + "</a>";
                }
                trHTML +=
                    "</td>";
            }
        });

        trHTML += "</tr>";

        return trHTML;
    };

    var webSocket = function(websocketUrl, prependTo, maxKillID) {
        ws = new WebSocket(websocketUrl);
        ws.onmessage = function(event) {
            var data = JSON.parse(event["data"]);
            if(data.killID > maxKillID && (typeof data.killTime_str != "undefined" && data.killTime_str !== null)) {
                maxKillID = data.killID;
                var trHTML = generateKillList(data);
                $(prependTo).prepend(trHTML);
                $("[data-toggle='tooltip']").tooltip();
            }
        };
    };

    var loadMoreOnScroll = function(url) {
        var page = 1, isPreviousPageLoaded = true;
        $(window).scroll(function() {
            if($(document).height() - 500 <= $(window).scrollTop() + $(window).height()) {
                if(isPreviousPageLoaded) {
                    isPreviousPageLoaded = false;
                    var address = window.location.origin + url + (page + 1) + "/";
                    $.ajax({
                        type: "GET",
                        url: address,
                        data: "{}",
                        contentType: "application/json; charset=utf-8",
                        dataType: "json",
                        cache: false,
                        success: function(data) {
                            var trHTML = "";
                            $.each(data, function(i, kill) {
                                trHTML += generateKillList(kill);
                            });

                            $("#killlist").append(trHTML);

                            page++;
                            isPreviousPageLoaded = true;
                        },
                        error: function(msg) {
                            alert(msg.responseText);
                        }
                    });
                }
            }
        });
    };

    // Turn on CORS support for jQuery
    jQuery.support.cors = true;

    // Define the current origin url (eg: https://neweden.xyz)
    var currentOrigin = window.location.origin;

    // Get the data from the JSON API and output it as a killlist...
    $.ajax({
        // Define the type of call this is
        type: "GET",
        // Define the url we're getting data from
        url: currentOrigin + url,
        // Predefine the data field.. it's just an empty array
        data: "{}",
        // Define the content type we're getting
        contentType: "application/json; charset=utf-8",
        // Set the data type to json
        dataType: "json",
        // Don't cache it - the backend does that for us
        cache: false,
        success: function (data) {
            var maxKillID = 0;
            var trHTML = "";
            // data-toggle='tooltip' data-html='true' data-placement='left' title='"+kill.killTime.toString()+"'
            // Now for each element in the data we just got from the json api, we'll build up some html.. ugly.. ugly.. html
            $.each(data, function (i, kill) {
                maxKillID = highestKillID(kill.killID);
                trHTML += generateKillList(kill); //This isn't exactly pretty - but it does the job for now... Until someone decides to cause an argument over it, and finally fixes it
            });

            // Append the killlist element to the killlist table
            $("#killlist").append(trHTML);

            // Turn on popovers
            $('[data-toggle="popover"]').popover();

            // Turn on tooltips for the killlist - having this outside apparently turns it off... js.. /o\
            $("[data-toggle='tooltip']").tooltip();

            // Fire up the Websocket
            webSocket("wss://ws.eve-kill.net/kills", "#killlist", maxKillID);

            // Turn on loading more on scroll
            loadMoreOnScroll(url);
        },
        error: function (msg) {
            alert(msg.responseText);
        }
    });
};
//@todo fix so that it unloads data when it gets over 1k items
generateKillList("/api/killlist/latest/");