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

var characterHistory = function(url, append) {
    var generateHTML = function(data) {
        var html = "";
        html += "<table class='kb-table'>";
        html += "<thead>";
        html += "<tr class='kb-table-header'>";
        html += "<td></td>";
        html += "<td>Name</td>";
        html += "<td>Joined</td>";
        html += "</tr>";
        html += "</thead>";
        html += "<tbody>";

        odd = false;
        data.history.forEach(function(corp) {
            html += "<tr class=";
            if(odd == true)
                html += "kb-table-row-odd";
            else
                html += "kb-table-row-even";
            html += ">";

            html += "<td class=kb-table-imgcell><img src='https://imageserver.eveonline.com/Corporation/" + corp.corporationID + "_32.png'/></td>";
            html += "<td style='text-align: center'><a href='/corporation/" + corp.corporationID +"/'>" + corp.corporationName + "</a></td>";
            html += "<td>" + corp.startDate + "</td>";
            html += "</tr>";
            odd = odd == false;
        });
        html += "</tbody>";
        html += "</table>";

        return html;
    };

    jQuery.support.cors = true;
    var currentOrigin = window.location.origin;
    $.ajax({
        type: "GET",
        url: currentOrigin + url,
        data: "{}",
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        cache: false,
        success: function (data) {
            var trHTML = "";
            trHTML += generateHTML(data);
            $(append).append(trHTML);
            turnOnFunctions();
        }
    });
};
