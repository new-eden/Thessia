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

var top10ListGenerator = function (url, type) {
    // Turn on CORS support for jQuery
    jQuery.support.cors = true;

    // Define the current origin url (eg: https://neweden.xyz)
    var currentOrigin = window.location.origin;
    var loop = 1;

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
            var h = '<table class="kb-table awardbox">' +
                '<tr>' +
                '<td class="kb-table-header">' + type + '</td>' +
                '</tr>' +
                '<tr class="kb-table-row-even">';

            // data-toggle='tooltip' data-html='true' data-placement='left' title='"+kill.killTime.toString()+"'
            // Now for each element in the data we just got from the json api, we'll build up some html.. ugly.. ugly.. html
            var totalKills = 0;
            $.each(data, function (i, kill) {
                totalKills += kill.count;
            });

            $.each(data, function (i, kill) {
                percent = (100 - ((kill.count / totalKills) * 100));
                if (loop == 1) {
                    h += '<table class="kb-subtable awardbox-list">' +
                        '<tr>' +
                        '<td class="awardbox-num">1.</td>' +
                        '<td colspan="2">' +
                        '<a class="kb-shipclass" href="/region/'+kill.regionID+'/">' + kill.regionName + '</a>' +
                        '</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td></td>' +
                        '<td>' +
                        '<div class="bar-background">' +
                        '<div class="bar" style="width: 100%;">&nbsp;</div>' +
                        '</div>' +
                        '</td>' +
                        '<td class="awardbox-count">' + kill.count + '</td>' +
                        '</tr>';
                } else {
                    totalKills = totalKills - kill.count;
                    h += '<tr>' +
                        '<td class="awardbox-num">'+loop+'</td>' +
                        '<td colspan="2"><a class="kb-shipclass" href="/region/'+kill.regionID+'/">' + kill.regionName + '</a></td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td></td>' +
                        '<td><div class="bar-background"><div class="bar" style="width: ' + percent + '%;">&nbsp;</div></td>' +
                        '<td class="awardbox-count">' + kill.count + '</td>' +
                        '</tr>';
                }
                loop++;
            });

            h += '<tr>' +
                '<td class="awardbox-comment" colspan="3">(Kills over last 7 days)</td>' +
                '</tr>' +
                '</table>' +
                '</td>' +
                '</tr>' +
                '</table>'

            // Append the killlist element to the killlist table
            $("#topListRegions").append(h);

            // Turn on tooltips, popovers etc.
            turnOnFunctions();
        }
    });
};

//@todo fix so that it unloads data when it gets over 1k items
top10ListGenerator("/api/stats/top10regions/", "Top 10 Regions");