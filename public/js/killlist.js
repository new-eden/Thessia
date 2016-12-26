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

var generateKillList = function(url, loadWebsocket, page, autoloadScroll) {
    var maxKillID = 0;
    var highestKillID = function(newID) {
        maxKillID = Math.max(maxKillID, newID);
        return maxKillID;
    };

    var generateKillList = function(kill, killCount) {
        var h = "";

        // Ship type portion
        if(isOdd(killCount)) {
            h += '<tr class="kb-table-row-kill kb-table-row-odd clickableRow" data-href="/kill/' + kill.killID + '/">';
        } else {
            h += '<tr class="kb-table-row-kill kb-table-row-even clickableRow" data-href="/kill/' + kill.killID + '/">';
        }
        h += '<td class="kb-table-imgcell">' +
            '<img class="rounded" data-trigger="tooltip" data-delay="0" data-content="'+kill.victim.shipTypeName+'" data-position="s" src="https://imageserver.eveonline.com/Type/'+ kill.victim.shipTypeID +'_32.png" style="width: 32px; height: 32px;"/>' +
            '</td>' +
            '<td class="kl-shiptype-text">' +
            '<div class="no_stretch kl-shiptype-text">' +
            '<b>'+ kill.victim.shipTypeName +'</b>' +
            '<br/>' +
            millionBillion(kill.totalValue) +
            '</div>' +
            '</td>';

        // Victim portion
        if(kill.victim.allianceID > 0) {
            h += '<td class="kb-table-imgcell">'+
                '<img class="rounded" data-trigger="tooltip" data-delay="0" data-content="'+ kill.victim.allianceName +'" data-position="s" src="https://imageserver.eveonline.com/Alliance/' + kill.victim.allianceID + '_32.png" style="border: 0px; width: 32px; height: 32px;" title="'+ kill.victim.allianceName +'" alt="'+ kill.victim.allianceName +'"/>' +
                '</td>';
        } else {
            h += '<td class="kb-table-imgcell">'+
                '<img class="rounded" data-trigger="tooltip" data-delay="0" data-content="'+kill.victim.corporationName+'" data-position="s" src="https://imageserver.eveonline.com/Corporation/' + kill.victim.corporationID + '_32.png" style="border: 0px; width: 32px; height: 32px;" title="'+kill.victim.corporationName+'" alt="'+kill.victim.corporationName+'"/>' +
                '</td>';
        }

        h += '<td class="kl-victim-text">' +
            '<div class="no_stretch kl-victim-text">' +
            '<a href="/character/'+kill.victim.characterID+'/"><b>'+kill.victim.characterName+'</b></a><br/>';

        if(kill.victim.allianceID > 0) {
            h += '<a href="/alliance/'+kill.victim.allianceID+'/">'+kill.victim.allianceName+'</a>';
        } else {
            h += '<a href="/corporation/'+kill.victim.corporationID+'/">'+kill.victim.corporationName+'</a>';
        }

        // Final blow portion
        kill.attackers.forEach(function(attacker) {
            if(attacker.finalBlow == 1) {
                h += '<td class="kb-table-imgcell">' +
                    '<img class="rounded" data-trigger="tooltip" data-delay="0" data-content="'+attacker.corporationName+'" data-position="s" src="https://imageserver.eveonline.com/Corporation/'+attacker.corporationID+'_32.png" style="border: 0px; width: 32px; height: 32px;" title="'+attacker.corporationName+'" alt="'+attacker.corporationName+'"/>' +
                    '</td>' +
                    '<td class="kl-finalblow">' +
                    '<div class="no_stretch kl-finalblow">' +
                    '<a href="/character/'+attacker.characterID+'/"><b>'+attacker.characterName+'</b></a>' +
                    '<br/>' +
                    '<a href="/corporation/'+attacker.corporationID+'/">'+attacker.corporationName+'</a>' +
                    '</div>' +
                    '</td>';
            }
        });

        // Location
        var attackerCount = kill.attackers.length;
        var date = new Date(kill.killTime);
        var killTime = ("0" + date.getHours()).slice(-2) + ":" + ("0" + date.getMinutes()).slice(-2) + ":" + ("0" + date.getSeconds()).slice(-2);
        
        h += '<td class="kb-table-cell kl-location">' +
            '<div class="kl-location">'+kill.regionName+', '+kill.solarSystemName+' (' + parseFloat(kill.solarSystemSecurity).toFixed(2) + ')<br/>' +
            '</div>' +
            '<div class="kl-inv-comm">' +
            '<img src="/img/comment_white.gif" alt="C:"/><span class="disqus-comment-count" data-disqus-identifier="'+kill.killID+'">0&nbsp;</span>' +
            '</div>' +
            '<div class="kl-inv-comm">' +
            '<img src="/img/involved10_10.png" alt="I:"/> ' + attackerCount +
            '</div>' +
            '<div class="kl-date">' +
            '<a href="/related/"><b>'+kill.killTime_str+'</b></a>' +
            '</div>' +
            '</td>' +
            '</tr>';

        return h;
    };

    var webSocket = function(websocketUrl, prependTo, maxKillID) {
        ws = new WebSocket(websocketUrl);
        ws.onmessage = function(event) {
            var data = JSON.parse(event["data"]);
            if(data.killID > maxKillID && (typeof data.killTime_str != "undefined" && data.killTime_str !== null)) {
                maxKillID = data.killID;
                var trHTML = generateKillList(data);
                $(prependTo).prepend(trHTML);
                turnOnFunctions();
            }
        };
    };

    var loadMoreOnScroll = function(url, page) {
        page = parseInt(page);
        var isPreviousPageLoaded = true;

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
                            var killCount = 0;
                            $.each(data, function(i, kill) {
                                trHTML += generateKillList(kill, killCount);
                                killCount++;
                            });

                            $("#killlist").append(trHTML);
                            turnOnFunctions();
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
        url: currentOrigin + url + page + "/",
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

            turnOnFunctions();
            if (loadWebsocket == true) {
                // Fire up the Websocket
                webSocket("wss://ws.neweden.xyz/kills", "#killlist", maxKillID);
            }

            if(autoloadScroll == true) {
                // Turn on loading more on scroll
                loadMoreOnScroll(url, page);
            }
        }
    });
};