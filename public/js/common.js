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

var millionBillion = function (isk) {
    if (isk > 1000000000) { // Billion
        return (isk / 1000000000).toFixed(2) + "b ISK";
    }
    return (isk / 1000000).toFixed(2) + "m ISK";
};

var truncate = function (string, length) {
    return string.length > length ? string.substring(0, length - 3) + "..." : string;
};

var htmlGenerator = function (url, generateFrom, outputTo) {
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
            // data-toggle='tooltip' data-html='true' data-placement='left' title='"+kill.killTime.toString()+"'
            // Now for each element in the data we just got from the json api, we'll build up some html.. ugly.. ugly.. html
            $.each(data, function (i, kill) {
                trHTML += window[generateFrom](kill); //This isn't exactly pretty - but it does the job for now... Until someone decides to cause an argument over it, and finally fixes it
            });

            // Append the killlist element to the killlist table
            $(outputTo).append(trHTML);

            // Turn on popovers
            $('[data-toggle="popover"]').popover()

            // Turn on tooltips for the killlist - having this outside apparently turns it off... js.. /o\
            $("[data-toggle='tooltip']").tooltip()
        },
        error: function (msg) {
            alert(msg.responseText);
        }
    });
};