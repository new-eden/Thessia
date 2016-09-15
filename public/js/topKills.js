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

var generateTopKillsHeader = function (kill) {
    var h = "";

    h += '<th data-trigger="tooltip" data-delay="0" data-position="s" data-cssclass="infotip" data-content="';

    if (kill.victim.characterID > 0) {
        h += '<img class=\'rounded\' src=https://imageserver.eveonline.com/Character/' + kill.victim.characterID + '_128.jpg/><br>';
        h += 'Character: ' + kill.victim.characterName + '<br>';
    } else {
        h += '<img class=\'rounded\' src=https://imageserver.eveonline.com/Corporation/' + kill.victim.corporationID + '_128.png/><br>';
    }

    h += 'Corporation: ' + kill.victim.corporationName + '<br>';

    if (kill.victim.allianceID > 0) {
        h += 'Alliance: ' + kill.victim.allianceName + '<br>';
    }
    h +=
        'System: ' + kill.solarSystemName + '<br>' +
        'Near: ' + kill.near + '<br>' +
        'Region: ' + kill.regionName + '<br>' +
        'Fitting Value: ' + millionBillion(kill.fittingValue) + '<br>' +
        'Ship value: ' + millionBillion(kill.shipValue) + '<br>"' +
    '">' +
    '<a href="/kill/'+kill.killID+'/"><img class="rounded" src="https://imageserver.eveonline.com/Render/' + kill.victim.shipTypeID + '_128.png"><br>' + kill.victim.shipTypeName + '<br>' + millionBillion(kill.totalValue) + '</a>' +
    '</th>';

    return h;
    /*var trHTML = "";


     // The HTML to populate pr. top kill
     trHTML += '' +
     '<div style="text-align: center; height: 140px;" data-container="body" data-trigger="hover" data-toggle="popover" data-placement="bottom" data-html="true" ' +
     'data-content="">' +
     '<a href="/kill/' + kill.killID + '/"><img class="img-circle" src="https://imageserver.eveonline.com/Render/' + kill.victim.shipTypeID + '_128.png" height="90"/></a>' +
     '<br/>' +
     '<span class="hidden-xs">' +
     '<a href="/character/' + kill.victim.characterID + '/">' + truncate(kill.victim.shipTypeName, 20) + '</a>' +
     '<br/>' +
     '</span>' +
     '<h6>' + millionBillion(kill.totalValue) + '</h6>' +
     '<br/>' +
     '</div>';
     return trHTML;*/
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
    url: currentOrigin + "/api/stats/mostvaluablekillslast7days/6/",
    // Predefine the data field.. it's just an empty array
    data: "{}",
    // Define the content type we're getting
    contentType: "application/json; charset=utf-8",
    // Set the data type to json
    dataType: "json",
    // Don't cache it - the backend does that for us
    cache: false,
    success: function (data) {
        var h = "";
        h +=
            '<table style="width: 100%;">' +
            '<tbody>' +
            '<tr>';
        // data-toggle='tooltip' data-html='true' data-placement='left' title='"+kill.killTime.toString()+"'
        // Now for each element in the data we just got from the json api, we'll build up some html.. ugly.. ugly.. html
        $.each(data, function (i, kill) {
            h += generateTopKillsHeader(kill); //This isn't exactly pretty - but it does the job for now... Until someone decides to cause an argument over it, and finally fixes it
        });

        h += '</tr>' +
            '</tbody>' +
            '</table>';

        // Append the killlist element to the killlist table
        $("#topKills").append(h);

        // Turn on tooltips, popovers etc.
        turnOnFunctions();
    }
});