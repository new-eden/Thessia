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

var generateBattles = function(page) {
    var generateHTML = function(data) {
        var h = '';
        h ='<table class="kb-table contractlist kb-table-rows">' +
            '<thead>' +
                '<tr class="kb-table-header">' +
                '<td style="width: 25%;">System / Region</td>' +
                '<td class="kb-date">Start date</td>' +
                '<td class="kb-date">End date</td>' +
                '<td class="killcount">Kills</td>' +
                '<td class="iskcount">Involved Characters</td>' +
                '<td class="iskcount">Involved Corporations</td>' +
                '<td class="iskcount">Involved Alliances</td>' +
                '</tr>' +
            '</thead>' +
            '<tbody>';
            data.forEach(function(b) {
                var sTime = new Date(b.startTime);
                var eTime = new Date(b.endTime);
                var startTime = sTime.toISOString().slice(0,10).replace(/-/g,"/") + " " + ("0" + sTime.getHours()).slice(-2) + ":" + ("0" + sTime.getMinutes()).slice(-2) + ":" + ("0" + sTime.getSeconds()).slice(-2);
                var endTime = eTime.toISOString().slice(0,10).replace(/-/g,"/") + " " + ("0" + eTime.getHours()).slice(-2) + ":" + ("0" + eTime.getMinutes()).slice(-2) + ":" + ("0" + eTime.getSeconds()).slice(-2);
                h +=
                    '<tr class="clickableRow" data-href="/battles/' + b.battleID + '/">' +
                        '<td class="name">' + b.solarSystemName + ' / ' + b.regionName + '</td>' +
                        '<td class="kb-date" style="text-align: center;">' + startTime + '</td>' +
                        '<td class="kb-date" style="text-align: center;">' + endTime + '</td>' +
                        '<td class="kl-kill" style="text-align: center;">' + b.killCount + '</td>' +
                        '<td class="kl-kill" style="text-align: center;">' + b.involvedCount.characters + '</td>' +
                        '<td class="kl-loss" style="text-align: center;">' + b.involvedCount.corporations + '</td>' +
                        '<td class="kl-loss" style="text-align: center;">' + b.involvedCount.alliances + '</td>' +
                    '</tr>';
            });
        h +='</tbody>' +
            '</table>';

        return h;
    };

    jQuery.support.cors = true;
    var currentOrigin = window.location.origin;
    $.ajax({
        type: "GET",
        url: currentOrigin + "/api/battlereport/battles/" + page + "/",
        data: "{}",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
        success: function (data) {
            var trHTML = "";
            trHTML += generateHTML(data);

            $("#battles").append(trHTML);

            turnOnFunctions();
        }
    });
};