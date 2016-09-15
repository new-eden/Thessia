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
                '<td class="kb-table-header">'+type+'</td>' +
                '</tr>' +
                '<tr class="kb-table-row-even">' +
                '<td>';

            // data-toggle='tooltip' data-html='true' data-placement='left' title='"+kill.killTime.toString()+"'
            // Now for each element in the data we just got from the json api, we'll build up some html.. ugly.. ugly.. html
            var totalKills = 0;
            $.each(data, function (i, kill) {
                totalKills += kill.count;
            });

            $.each(data, function (i, kill) {
                percent = (100 - ((kill.count / totalKills) * 100));
                if (loop == 1) {
                    h += '<table class="kb-subtable awardbox-top">' +
                        '<tr class="kb-table-row-odd">' +
                        '<td><img class="rounded" data-trigger="tooltip" data-delay="0" data-position="e" data-content="' + kill.characterName + '" src="https://imageserver.eveonline.com/Character/' + kill.characterID + '_64.jpg" title="' + kill.characterName + '" alt="' + kill.characterName + '" height="64" width="64" /></td>' +
                        '<td><img class="rounded" src="/img/awards/eagle.png" alt="award" height="64" width="64" /></td>' +
                        '</tr>' +
                        '</table>' +
                        '<table class="kb-subtable awardbox-list">' +
                        '<tr>' +
                        '<td class="awardbox-num">1.</td>' +
                        '<td colspan="2"><a class="kb-shipclass" href="/character/'+kill.characterID+'" data-trigger="tooltip" data-cssclass="infotip" data-delay="0" data-position="e" data-content="' +
                        '<img src=https://imageserver.eveonline.com/Character/' + kill.characterID + '_128.jpg/><br>' +
                        'Name: ' + kill.characterName + '<br>' +
                        'Corporation: ' + kill.corporationName + '<br>';
                    if (kill.allianceID > 0) {
                        h += 'Alliance: ' + kill.allianceName + '<br>';
                    }
                    h += 'Kills: ' + kill.kills + '<br>' +
                        'Losses: ' + kill.losses + '<br>' +
                        'Points: ' + kill.points + '<br>"' +
                        '>' + kill.characterName + '</a></td>' +
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
                        '<td colspan="2"><a class="kb-shipclass" href="/character/'+kill.characterID+'" data-trigger="tooltip" data-cssclass="infotip" data-delay="0" data-position="e" data-content="' +
                        '<img src=https://imageserver.eveonline.com/Character/' + kill.characterID + '_128.jpg/><br>' +
                        'Name: ' + kill.characterName + '<br>' +
                        'Corporation: ' + kill.corporationName + '<br>';
                    if (kill.allianceID > 0) {
                        h += 'Alliance: ' + kill.allianceName + '<br>';
                    }
                    h += 'Kills: ' + kill.kills + '<br>' +
                        'Losses: ' + kill.losses + '<br>' +
                        'Points: ' + kill.points + '<br>"' +
                        '>' + kill.characterName + '</a></td>' +
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
            $("#topListCharacter").append(h);

            // Turn on tooltips, popovers etc.
            turnOnFunctions();
        },
        error: function (msg) {
            alert(msg.responseText);
        }
    });
};

//@todo fix so that it unloads data when it gets over 1k items
top10ListGenerator("/api/stats/top10characters/", "Top 10 Characters");