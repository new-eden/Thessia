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

var generateTopKillsHeader = function(kill) {
    var trHTML = "";

    // The HTML to populate pr. top kill
    trHTML += '' +
        '<div class="col-sm-2" style="text-align: center; height: 140px;">' +
            '<img data-toggle="tooltip" data-placement="top" title="'+kill.victim.shipTypeName+'" class="img-circle" src="https://imageserver.eveonline.com/Render/'+kill.victim.shipTypeID+'_64.png" height="64" width="64"/>' +
            '<br/>' +
            '<span class="hidden-xs">' +
                '<a href="#">'+kill.victim.characterName+'</a>' +
                '<br/>' +
            '</span>' +
            '<h6>' + millionBillion(kill.totalValue) + '</h6>' +
            '<br/>' +
        '</div class="col-sm-2">';
    return trHTML;
};

//@todo fix so that it unloads data when it gets over 1k items
htmlGenerator("/api/stats/mostvaluablekillslast7days/6/", "generateTopKillsHeader", "#mostValuableKills");

//data-toggle='tooltip' data-placement='left' title='" + kill.victim.shipTypeName + "'