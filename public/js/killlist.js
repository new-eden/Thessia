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

var generateKillList = function(kill) {
    var trHTML = "";

    // Convert the killTime from MongoISODateUTC thing to an ISOString for timeAgo
    // Stitch together the html that we want to output..
    trHTML += "" +
        "<tr onclick=\"window.location='/kill/" + kill.killID + "/'\">" +
            "<th class='hidden-sm-down' scope='row' data-toggle='tooltip' data-placement='right' title='" + new Date(kill.killTime).toString() + "'>" +
                "<span data-livestamp='" + kill.killTime + "'></span>" +
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
                "<a href='/corporation/" + kill.victim.corporationID + "/'>" + truncate(kill.victim.corporationName, 20) + "</a> ";
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

var webSocket = function(websocketUrl, prependTo) {
    var trHTML = "";
    var currentTime = new Date().getTime();
    ws = new WebSocket(websocketUrl);
    ws.onmessage = function(event) {
        var data = JSON.parse(event["data"]);
        var tmpTime = new Date(data.killTime);
        var killmailTime = tmpTime.getTime();
        if(data.killID > 0 && (killmailTime >= currentTime)) {
            trHTML = generateKillList(data);
            $(prependTo).prepend(trHTML);
        }
    };
};

//@todo fix so that it unloads data when it gets over 1k items
webSocket("wss://ws.eve-kill.net/kills", "#killlist");
htmlGenerator("/api/killlist/latest/", "generateKillList", "#killlist");