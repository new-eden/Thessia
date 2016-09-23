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

var corporationInfoTop = function(url) {
    var generateHTML = function(data) {
        var h = "";

        h +=
            '<table class="kb-table" style="width: auto;">' +
            '<colgroup>' +
            '<col class="logo">' +
            '<col class="attribute-name">' +
            '<col class="attribute-data">' +
            '</colgroup>' +
            '<tbody>' +
            '<tr class="kb-table-row-even">' +
            '<td rowspan="7">' +
            '<a href="/corporation/' + data.corporationID + '/"><img class="rounded" src="https://imageserver.eveonline.com/Corporation/' + data.corporationID + '_128.png" alt="portrait"></a>' +
            '<a href="/alliance/' + data.allianceID + '/"><img class="rounded" src="https://imageserver.eveonline.com/Alliance/' + data.allianceID + '_128.png" alt="portrait"></a>' +
            '</td>' +
            '</tr>';

        if (data.allianceID > 0) {
            h +=
                '<tr class="kb-table-row-even">' +
                '<td>Alliance:</td>' +
                '<td>' +
                '<a href="/alliance/' + data.allianceID + '/">' + data.allianceName + '</a>' +
                '</td>' +
                '</tr>';
        }

         h+=
            '<tr class="kb-table-row-even">' +
            '<td>Kills:</td>' +
            '<td class="kl-kill">';
            if(data.kills > 0) {
                h += Math.round(data.kills);
            } else {
                h += 0;
            }
        h +=
            '<tr class="kb-table-row-even">' +
            '<td>Losses:</td>' +
            '<td class="kl-loss">';
            if(data.losses > 0) {
                h += data.losses;
            } else {
                h += 0;
            }
        h +=
            '</td>' +
            '</tr>' +
            '<tr class="kb-table-row-even">' +
            '<td>Chance of enemy survival:</td>' +
            '<td>' +
            '<span style="color:#AA0000;">';
            if(data.losses > 0 && data.kills > 0) {
                h += Math.round((data.losses / data.kills) * 100)
            }
            else {
                h += "100";
            }
        h += '%</span>' +
            '</td>' +
            '</tr>' +
            '<tr class="kb-table-row-even">' +
            '<td>Efficiency:</td>' +
            '<td>' +
            '<span style="color:#00AA00;">';
            if(data.losses > 0 && data.kills > 0) {
                h += Math.round(100 - ((data.losses / data.kills) * 100));
            } else {
                h += "0";
            }
        h +=
            '%</span>' +
            '</td>' +
            '</tr>' +
            '</tbody>' +
            '</table>';

        return h;
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
            var trHTML = "";

            trHTML += generateHTML(data); //This isn't exactly pretty - but it does the job for now... Until someone decides to cause an argument over it, and finally fixes it

            // Append the killlist element to the killlist table
            $("#info").append(trHTML);

            turnOnFunctions();
        }
    });
}
