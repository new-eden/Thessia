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

var top10ListGenerator = function (url, type, outputTo) {
    var trHTML = "";
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
            trHTML +=
                '<table class="table">' +
                '<thead class="thead-inverse">' +
                '<tr><th>#</th><th>' + type + '</th><th>Kills</th></tr>' +
                '</thead>' +
                '<tbody>';

            var number = 1;

            // data-toggle='tooltip' data-html='true' data-placement='left' title='"+kill.killTime.toString()+"'
            // Now for each element in the data we just got from the json api, we'll build up some html.. ugly.. ugly.. html
            $.each(data, function (i, kill) {
                trHTML +=
                    '<tr data-container="body" data-trigger="hover" data-toggle="popover" data-placement="left" data-html="true" data-content="' +
                    '<div class=text-xs-center><img class=img-circle src=https://imageserver.eveonline.com/Alliance/'+kill.allianceID+'_128.png/><div><br>' +
                    'Alliance: ' + kill.allianceName + '<br>' +
                    'Kills: ' + kill.kills + '<br>' +
                    'Losses: ' + kill.losses + '<br>' +
                    'Points: ' + kill.points + '<br>">' +
                    '<th scope="row">'+number+'</th>' +
                    '<td>'+ kill.allianceName +'</td>' +
                    '<td>'+ kill.count +'</td>' +
                    '</tr>';

                number++;
            });

            trHTML +=
                '</tbody>' +
                '</table>';

            // Append the killlist element to the killlist table
            $(outputTo).append(trHTML);

            // Turn on popovers
            $('[data-toggle="popover"]').popover();
        },
        error: function (msg) {
            alert(msg.responseText);
        }
    });
};

//@todo fix so that it unloads data when it gets over 1k items
top10ListGenerator("/api/stats/top10alliances/", "Top 10 Alliances", "#topAlliances");