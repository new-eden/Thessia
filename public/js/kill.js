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

var generateKillData = function(killID) {
    // Get Data
    // Turn on CORS support for jQuery
    jQuery.support.cors = true;

    // Define the current origin url (eg: https://neweden.xyz)
    var currentOrigin = window.location.origin;
    var killURL = currentOrigin + "/api/kill/mail/killID/" + killID + "/";

    // Get the data from the JSON API and output it as a killlist...
    $.ajax({
        // Define the type of call this is
        type: "GET",
        // Define the url we're getting data from
        url: killURL,
        // Predefine the data field.. it's just an empty array
        data: "{}",
        // Define the content type we're getting
        contentType: "application/json; charset=utf-8",
        // Set the data type to json
        dataType: "json",
        // Don't cache it - the backend does that for us
        cache: false,
        success: function (data) {
            pointHTML(data);
            topDamageDealerAndFinalBlow(data);
            topVictimInformationBox(data);
            fittingWheel(data);
            itemDetail(data);
            involvedPartiesInfo(data);
            involvedPartiesList(data);
        },
        error: function (msg) {
            alert(msg.responseText);
        }
    });

    // Generate Point HTML
    var pointHTML = function(data) {
        var h =
            '<table class="kb-table kb-box">' +
                '<tr>' +
                    '<td class="kb-table-header">Points</td>' +
                '</tr>' +
                '<tr class="kb-table-row-even">' +
                    '<td>' +
                        '<div class="menu-wrapper">' +
                            '<div class="kill-points">' + Math.round(data.pointValue) + '</div>' +
                        '</div>' +
                    '</td>' +
                '</tr>' +
            '</table>';

        $("#points").append(h);
    };
    var topDamageDealerAndFinalBlow = function(data) {
        var h = "";
        var loop = 0;
        data.attackers.forEach(function(attacker) {
            // On the first loop, we get the top damage dealer, right off the bat
            if (loop == 0) {
                h +=
                    '<table class="kb-table kb-box">' +
                    '<tr>' +
                    '<td class="kb-table-header" colspan="2">Top Damage Dealer</td>' +
                    '</tr>' +
                    '<tr class="kb-table-row-odd">' +
                        '<td class="finalblow">' +
                            '<div class="menu-wrapper">' +
                                '<a href="/character/'+attacker.characterID+'/">' +
                                    '<img class="finalblow rounded" src="https://imageserver.eveonline.com/Character/'+attacker.characterID+'_64.jpg" alt="'+attacker.characterName+'" title="'+attacker.characterName+'" />' +
                                '</a>' +
                            '</div>' +
                        '</td>' +
                        '<td class="finalblow">' +
                            '<div class="menu-wrapper">' +
                                '<a href="/ship/'+attacker.shipTypeID+'/">' +
                                    '<img class="finalblow rounded" src="https://imageserver.eveonline.com/Type/'+attacker.shipTypeID+'_64.png" alt="'+attacker.shipTypeName+'" title="'+attacker.shipTypeName+'" />' +
                                '</a>' +
                            '</div>' +
                        '</td>' +
                    '</tr>' +
                    '</table>';
            }

            // FinalBlow
            if (attacker.finalBlow == 1) {
                h +=
                    '<table class="kb-table kb-box">' +
                        '<tr>' +
                            '<td class="kb-table-header" colspan="2">Final Blow</td>' +
                        '</tr>' +
                        '<tr class="kb-table-row-odd">' +
                            '<td class="finalblow">' +
                                '<div class="menu-wrapper">' +
                                    '<a href="/character/'+attacker.characterID+'/">' +
                                        '<img class="finalblow rounded" src="https://imageserver.eveonline.com/Character/'+attacker.characterID+'_64.jpg" alt="'+attacker.characterName+'" title="'+attacker.characterName+'" />' +
                                    '</a>' +
                                '</div>' +
                            '</td>' +
                            '<td class="finalblow">' +
                                '<div class="menu-wrapper">' +
                                    '<a href="/ship/'+attacker.shipTypeID+'/">' +
                                        '<img class="finalblow rounded" src="https://imageserver.eveonline.com/Type/'+attacker.shipTypeID+'_64.png" alt="'+attacker.shipTypeName+'" title="'+attacker.shipTypeName+'" />' +
                                    '</a>' +
                                '</div>' +
                            '</td>' +
                        '</tr>' +
                    '</table>';
            }
            loop++;
        });

        $("#topDamageAndFinal").append(h);
    };
    var topVictimInformationBox = function(data) {
        var h = "";
        h +=
            '<div class="kl-detail-vicpilot">' +
                '<table class="kb-table">' +
                    '<col class="logo"/>' +
                    '<col class="attribute-name"/>' +
                    '<col class="attribute-data"/>' +
                    '<tr class="kb-table-row-even" >' +
                        '<td class="logo" rowspan="3">' +
                        '<img class="rounded" src="https://imageserver.eveonline.com/Character/' + data.victim.characterID + '_64.jpg" alt="' + data.victim.characterName + '"/>' +
                        '</td>';
                        if(data.victim.characterID > 0) {
                            h +=
                                '<td>Victim:</td>' +
                                '<td><a href="/ship/' + data.victim.characterID + '/">' + data.victim.characterName + '</a></td>';
                        } else {
                            h += '<td>&nbsp;</td><td>&nbsp;</td>';
                        }
                    h += '</tr>' +
                    '<tr class="kb-table-row-odd">' +
                        '<td>Corporation:</td>' +
                        '<td><a href="/system/' + data.victim.corporationID + '/">' + data.victim.corporationName + '</a>';
                        if(data.victim.factionID > 0) {
                            h += '(<a href="/faction/' + data.victim.factionID + '/">' + data.victim.factionName + '</a>)</td>';
                        }
                    h += '</tr>' +
                    '<tr class="kb-table-row-even">' +
                        '<td>Alliance:</td>' +
                        '<td><a href="/alliance/' + data.victim.allianceID + '/">' + data.victim.allianceName + '</a></td>' +
                    '</tr>' +
                '</table>' +
            '</div>';

        h +=
            '<div class="kl-detail-vicship">' +
                '<table class="kb-table">' +
                    '<col class="logo"/>' +
                    '<col class="attribute-name"/>' +
                    '<col class="attribute-data"/>' +
                    '<tr class="kb-table-row-even" >' +
                        '<td class="logo" rowspan="3">' +
                            '<img class="rounded" src="https://imageserver.eveonline.com/Type/' + data.victim.shipTypeID + '_64.png" alt="' + data.victim.shipTypeName + '"/>' +
                        '</td>' +
                        '<td>Ship:</td>' +
                        '<td><a href="/ship/' + data.victim.shipTypeID + '/">' + data.victim.shipTypeName + '</a></td>' +
                    '</tr>' +
                    '<tr class="kb-table-row-odd">' +
                        '<td>Location:</td>' +
                        '<td><a href="/system/' + data.solarSystemID + '/">' + data.solarSystemName + '</a> (<a href="/region/' + data.regionID + '/">' + data.regionName + '</a>)</td>' +
                    '</tr>' +
                    '<tr class="kb-table-row-even">' +
                        '<td>Date:</td>' +
                        '<td>' + data.killTime_str + '</td>' +
                    '</tr>' +
                    '<tr class="kb-table-row-odd">' +
                        '<td colspan="2">ISK Loss at time of kill:</td>' +
                        '<td>' + millionBillion(data.totalValue) + '</td>' +
                    '</tr>' +
                    '<tr class="kb-table-row-even">' +
                        '<td colspan="2">Total Damage Taken:</td>' +
                        '<td>' + format(Math.round(data.victim.damageTaken)) + '</td>' +
                    '</tr>' +
                    '<tr class="kb-table-row-odd">' +
                        '<td colspan="2">Near:</td>' +
                        '<td>' + data.near + '</td>' +
                    '</tr>' +
                '</table>' +
            '</div>';

        $("#topVictimInfo").append(h);
    };
    var fittingWheel = function(data) {
        var h = "";

        h += '' +
        '<div class="kl-detail-fitting">' +
            '<div class="fitting-panel" style="position:relative; height:398px; width:398px;" title="fitting">' +
            '<div id="mask" class="fit-slot-bg">' +
            '<img style="height:398px; width:398px;" src="/panel/tyrannis.png" alt="" /></div>';

        var highCnt = 1;
        var medCnt = 1;
        var lowCnt = 1;
        var rigCnt = 1;
        var subCnt = 1;
        var highAmmoCnt = 1;
        var medAmmoCnt = 1;
        var id = "";
        var classText = "";
        var itemCategory = 7;
        var ammoCategory = 8;
        var highSlotFlags = [27, 28, 29, 30, 31, 32, 33, 34]; // Implant is 89
        var medSlotFlags = [19, 20, 21, 22, 23, 24, 25, 26];
        var lowSlotFlags = [11, 12, 13, 14, 15, 16, 17, 18];
        var rigSlotFlags = [92, 93, 94, 95, 96, 97, 98, 99];
        var subSystemFlags = [125, 126, 127, 128, 129, 130, 131, 132];
        var highSlotStyle = {
            high1: 'left:73px; top:60px;',
            high2: 'left:102px; top:42px;',
            high3: 'left:134px; top:27px;',
            high4: 'left:169px; top:21px;',
            high5: 'left:203px; top:22px;',
            high6: 'left:238px; top:30px;',
            high7: 'left:270px; top:45px;',
            high8: 'left:295px; top:64px;'
        };
        var medSlotStyle = {
            med1: 'left:26px; top:140px;',
            med2: 'left:24px; top:176px;',
            med3: 'left:23px; top:212px;',
            med4: 'left:30px; top:245px;',
            med5: 'left:46px; top:278px;',
            med6: 'left:69px; top:304px;',
            med7: 'left:100px; top:328px;',
            med8: 'left:133px; top:342px;'
        };
        var lowSlotStyle = {
            low1: 'left:344px; top:143px;',
            low2: 'left:350px; top:178px;',
            low3: 'left:349px; top:213px;',
            low4: 'left:340px; top:246px;',
            low5: 'left:323px; top:277px;',
            low6: 'left:300px; top:304px;',
            low7: 'left:268px; top:324px;',
            low8: 'left:234px; top:338px;'
        };
        var rigSlotStyle = {
            rig1: 'left:148px; top:259px;',
            rig2: 'left:185px; top:267px;',
            rig3: 'left:221px; top:259px;'
        };
        var subSlotStyle = {
            sub1: 'left:117px; top:131px;',
            sub2: 'left:147px; top:108px;',
            sub3: 'left:184px; top:98px;',
            sub4: 'left:221px; top:107px;',
            sub5: 'left:250px; top:131px;'
        };
        var highAmmoSlotStyle = {
            high1l: 'left:94px; top:88px;',
            high2l: 'left:119px; top:70px;',
            high3l: 'left:146px; top:58px;',
            high4l: 'left:175px; top:52px;',
            high5l: 'left:204px; top:52px;',
            high6l: 'left:232px; top:60px;',
            high7l: 'left:258px; top:72px;',
            high8l: 'left:280px; top:91px;'
        };
        var medAmmoSlotStyle = {
            med1l: 'left:59px; top:154px;',
            med2l: 'left:54px; top:182px;',
            med3l: 'left:56px; top:210px;',
            med4l: 'left:62px; top:238px;',
            med5l: 'left:76px; top:265px;',
            med6l: 'left:94px; top:288px;',
            med7l: 'left:118px; top:305px;',
            med8l: 'left:146px; top:318px;'
        };

        // High slots
        h += '<div id="highx" class="fit-slot-bg">' +
            '<img src="/panel/8h.png" alt="" />' +
            '</div>';

        data.items.forEach(function(item) {
            if (inArray(highSlotFlags, item.flag) && item.categoryID == itemCategory) {
                id = "high" + highCnt;
                classText = "fit-module";
                if (item.qtyDestroyed > 0) {
                    classText += " fit-destroyed";
                }
                h += '<div id="' + id + '" class="' + classText + '" style="' + highSlotStyle[id] + '"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"></div>';
                highCnt++;
            }
        });

        // Med slots
        h += '<div id="midx" class="fit-slot-bg">' +
            '<img src="/panel/8m.png" alt="" />' +
            '</div>';

        data.items.forEach(function(item) {
            if(inArray(medSlotFlags, item.flag) && item.categoryID == itemCategory) {
                id = "med" + medCnt;
                classText = "fit-module";
                if(item.qtyDestroyed > 0) {
                    classText += " fit-destroyed";
                }
                h += '<div id="'+id+'" class="'+classText+'" style="'+medSlotStyle[id]+'"><img src="https://imageserver.eveonline.com/Type/'+item.typeID+'_32.png"></div>';
                medCnt++;
            }
        });

        // Low slots
        h += '<div id="lowx" class="fit-slot-bg">' +
            '<img src="/panel/8l.png" alt="" />' +
            '</div>';

        data.items.forEach(function(item) {
            if (inArray(lowSlotFlags, item.flag) && item.categoryID == itemCategory) {
                id = "low" + lowCnt;
                classText = "fit-module";
                if (item.qtyDestroyed > 0) {
                    classText += " fit-destroyed";
                }
                h += '<div id="' + id + '" class="' + classText + '" style="' + lowSlotStyle[id] + '"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"></div>';
                lowCnt++;
            }
        });

        // Rig slots
        h += '<div id="rigxx" class="fit-slot-bg">' +
            '<img src="/panel/3r.png" alt="" />' +
            '</div>';
        data.items.forEach(function(item) {
            if (inArray(rigSlotFlags, item.flag)) {
                id = "rig" + rigCnt;
                classText = "fit-module";
                if (item.qtyDestroyed > 0) {
                    classText += " fit-destroyed";
                }
                h += '<div id="' + id + '" class="' + classText + '" style="' + rigSlotStyle[id] + '"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"></div>';
                rigCnt++;
            }
        });

        // Sub slots
        h += '<div id="subx" class="fit-slot-bg">' +
            '<img src="/panel/5s.png" alt="" />' +
            '</div>';
        data.items.forEach(function(item) {
            if (inArray(subSystemFlags, item.flag)) {
                id = "sub" + subCnt;
                classText = "fit-module";
                if (item.qtyDestroyed > 0) {
                    classText += " fit-destroyed";
                }
                h += '<div id="' + id + '" class="' + classText + '" style="' + subSlotStyle[id] + '"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"></div>';
                subCnt++;
            }
        });

        // High Slot Ammo
        data.items.forEach(function(item) {
            if(inArray(highSlotFlags, item.flag) && item.categoryID == ammoCategory) {
                id = "high" + highAmmoCnt + "l";
                classText = "fit-ammo";
                if(item.qtyDestryoed > 0) {
                    classText += " fit.destroyed";
                }
                h += '<div id="'+id+'" class="'+classText+'" style="'+ highAmmoSlotStyle[id]+'"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"></div>';
                highAmmoCnt++;
            }
        });

        // Med Slot Ammo
        data.items.forEach(function(item) {
            if(inArray(medSlotFlags, item.flag) && item.categoryID == ammoCategory) {
                id = "med" + medAmmoCnt + "l";
                classText = "fit-ammo";
                if(item.qtyDestryoed > 0) {
                    classText += " fit.destroyed";
                }
                h += '<div id="'+id+'" class="'+classText+'" style="'+ medAmmoSlotStyle[id]+'"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"></div>';
                medAmmoCnt++;
            }
        });

        h +=
            '<div class="bigship"><img src="https://imageserver.eveonline.com/Render/'+data.victim.shipTypeID+'_256.png" alt="" /></div>';

        h += '</div>' +
        '</div>';

        $("#fittingWheel").append(h);
    };
    var itemDetail = function(data) {
        var slotPopulated = function (flags, item) {
            if (inArray(flags, item.flag)) {
                return true;
            }
            return false;
        };
        var h = "";
        var slotTypes = {
            "High Slot": [27, 28, 29, 30, 31, 32, 33, 34],
            "Medium Slot": [19, 20, 21, 22, 23, 24, 25, 26],
            "Low Slot": [11, 12, 13, 14, 15, 16, 17, 18],
            "Rig Slot": [92, 93, 94, 95, 96, 97, 98, 99],
            "Subsystem": [125, 126, 127, 128, 129, 130, 131, 132],
            "Drone Bay": [87],
            "Cargo Bay": [5],
            "Fuel Bay": [133],
            "Fleet Hangar": [155],
            "Fighter Bay": [158],
            "Fighter Launch Tubes": [159, 160, 161, 162, 163],
            "Ship Hangar": [90],
            "Ore Hold": [134],
            "Gas hold": [135],
            "Mineral hold": [136],
            "Salvage Hold": [137],
            "Ship Hold": [138],
            "Small Ship Hold": [139],
            "Medium Ship Hold": [140],
            "Large Ship Hold": [141],
            "Industrial Ship Hold": [142],
            "Ammo Hold": [143],
            "Quafe Bay": [154],
            "Structure Services": [164, 165, 166, 167, 168, 169, 170, 171],
            "Structure Fuel": [172],
            "Implants": [89]
        };
        var lossValue = 0;
        var dropValue = 0;
        h +=
            '<div class="kl-detail-shipdetails">' +
            '<div class="block-header">Ship details</div>' +
                '<table class="kb-table">';

                for (var key in slotTypes) {
                    var slotName = key;
                    var flags = slotTypes[key];
                    for(var slotKey in data.items) {
                        var item = data.items[slotKey];
                        if(slotPopulated(flags, item)) {
                            h += '<tr class="kb-table-row-evenslotbg">' +
                                '<th class="item-icon"></th>' +
                                '<th colspan="2"><b>' + slotName + '</b> </th>' +
                                '<th><b>Value</b></th>' +
                                '</tr>';
                            break;
                        }
                    }

                    data.items.forEach(function (item) {
                        if (inArray(flags, item.flag)) {
                            if (item.qtyDropped > 0) {
                                h +=
                                    '<tr class="kb-table-row-odd dropped">' +
                                    '<td class="item-icon"><a href="/type/' + item.typeID + '/"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"</a></td>' +
                                    '<td>' + item.typeName + '</td>' +
                                    '<td>' + item.qtyDropped + '</td>' +
                                    '<td>' + millionBillion(item.value) + '</td>' +
                                    '</tr>';

                                dropValue += item.value;
                            } else if (item.qtyDestroyed > 0) {
                                h +=
                                    '<tr class="kb-table-row-odd">' +
                                    '<td class="item-icon"><a href="/type/' + item.typeID + '/"><img src="https://imageserver.eveonline.com/Type/' + item.typeID + '_32.png"</a></td>' +
                                    '<td>' + item.typeName + '</td>' +
                                    '<td>' + item.qtyDestroyed + '</td>' +
                                    '<td>' + millionBillion(item.value) + '</td>' +
                                    '</tr>';

                                lossValue += item.value;
                            }
                        }
                    });
                }
                h +=
                '<tr class="kb-table-row-even summary itemloss">' +
                    '<td colspan="3"><div>Total Module Loss:</div></td>' +
                    '<td>' + millionBillion(lossValue) + '</td>' +
                '</tr>' +
                '<tr class="kb-table-row-odd summary itemdrop">' +
                    '<td colspan="3"><div>Total Module Drop:</div></td>' +
                    '<td>' + millionBillion(dropValue) + '</td>' +
                '</tr>' +
                '<tr class="kb-table-row-even summary itemdrop">' +
                    '<td colspan="3"><div>Total Fit Value:</div></td>' +
                    '<td>' + millionBillion(data.fittingValue) + '</td>' +
                '</tr>' +
                '<tr class="kb-table-row-odd summary shiploss">' +
                    '<td colspan="3"><div>Ship Loss:</div></td>' +
                    '<td>' + millionBillion(data.shipValue) + '</td>' +
                '</tr>' +
                '<tr class="kb-table-row-even summary totalloss">' +
                    '<td colspan="3">Total Loss at current prices:</td>' +
                    '<td>' + millionBillion(data.totalValue) + '</td>' +
                '</tr>' +
            '</table>' +
        '</div>';

        $("#itemDetail").append(h);

    };
    var involvedPartiesInfo = function(data) {
        var h = "";
        var attackerCount = data.attackers.length;
        var sortObject = function(obj) {
            var arr = [];
            for(var prop in obj) {
                if(prop != "") {
                    if (obj.hasOwnProperty(prop)) {
                        arr.push({"key": prop, "value": obj[prop]});
                    }
                }
            }

            arr.sort(function(a, b) { return b.value - a.value; });
            return arr;
        };
        var invAlliances = sortObject(data.attackers.reduce((p, c) => !(p[c.allianceName] ? p[c.allianceName]++ : p[c.allianceName] = 1) || p, {}));
        var invCorporations = sortObject(data.attackers.reduce((p, c) => !(p[c.corporationName] ? p[c.corporationName]++ : p[c.corporationName] = 1) || p, {}));
        var invShips = sortObject(data.attackers.reduce((p, c) => !(p[c.shipTypeName] ? p[c.shipTypeName]++ : p[c.shipTypeName] = 1) || p, {}));
        if(attackerCount > 4) {
            h +=
                '<div class="kl-detail-invsum">' +
                '<div class="involvedparties">Involved parties: ' + attackerCount +
                '<div id="invsumcollapse" style="float: right">' +
                '<a href="javascript:Toggle();">Show/Hide</a>' +
                '</div>' +
                '</div>';
            h +=
                '<div id="ToggleTarget">' +
                '<table class="kb-table">' +
                '<tr class="kb-table-row-even" >' +
                '<td class="invcorps">' +
                '<div class="no_stretch">';
                for(var key in invAlliances) {
                    var amount = invAlliances[key]["value"];
                    var name = invAlliances[key]["key"];

                    h += '<div class="kb-inv-parties">' +
                        '('+ amount +') '+ name +
                        '</div>';

                }

                for(var key in invCorporations) {
                    var amount = invCorporations[key]["value"];
                    var name = invCorporations[key]["key"];

                    h += '<div>' +
                        '('+ amount +') '+ name +
                        '</div>';

                }

            h += '<td class="invships">' +
                '<div class="no_stretch">';
                for(var key in invShips) {
                    var amount = invShips[key]["value"];
                    var name = invShips[key]["key"];

                    h += '('+amount+') '+name+' <br/>';
                }
                h +=
                    '</div>' +
                    '</td>' +
                    '</div>' +
                    '</td>' +
                    '</tr>' +
                    '</table>' +
                    '</div>' +
                    '</div>';

            $("#involvedPartiesInfo").append(h);
        }

    };
    var involvedPartiesList = function(data) {
        var h = "";
        var attackerCnt = 0;
        var totalDamage = data.victim.damageTaken;
        var html = function(attacker, odd) {
            var h = "";
            var classText = "kb-table-row-even";
            if(odd == true) {
                classText = "kb-table-row-odd";
            }
            h +=
            '<tr class="' + classText + '">' +
                '<td rowspan="5" class="logo" width="64">' +
                    '<a href="/character/' + attacker.characterID + '/">';
                        if(attacker.finalBlow == true) {
                            h += '<img class="finalblow rounded" src="https://imageserver.eveonline.com/Character/'+ attacker.characterID+'_64.jpg" title="' + attacker.characterName + '" alt="' + attacker.characterName + '" />';
                        } else {
                            h += '<img class="rounded" src="https://imageserver.eveonline.com/Character/'+ attacker.characterID+'_64.jpg" title="' + attacker.characterName + '"  alt="' + attacker.characterName + '" />';
                        }
            h +=
                    '</a>' +
                '</td>' +
                '<td rowspan="5" class="logo" width="64">' +
                    '<a href="/ship/'+attacker.shipTypeID+'/">';
                        if(attacker.finalBlow == true) {
                            h += '<img class="finalblow rounded" src="https://imageserver.eveonline.com/Type/'+attacker.shipTypeID+'_64.png" alt="'+attacker.shipTypeName+'" title="'+attacker.shipTypeName+'" />';
                        } else {
                            h += '<img class="rounded" src="https://imageserver.eveonline.com/Type/'+attacker.shipTypeID+'_64.png" alt="'+attacker.shipTypeName+'" title="'+attacker.shipTypeName+'" />';
                        }
            h +=    '</a>' +
                '</td>' +
                '<td>' +
                    '<a href="/corporation/' + attacker.corporationID+ '/"><b>' + attacker.corporationName + '</b></a>' +
                '</td>' +
            '</tr>' +
            '<tr class="' + classText + '">' +
                '<td>' +
                    '<a href="/alliance/' + attacker.allianceID + '/">' + attacker.allianceName + '</a>' +
                '</td>' +
            '</tr>' +
            '<tr class="' + classText + '">' +
                '<td>' +
                    '<a href="/ship/' + attacker.shipTypeID + '"><b>' + attacker.shipTypeName + '</b></a>' +
                '</td>' +
            '</tr>' +
            '<tr class="' + classText + '">' +
                '<td><a href="/type/' + attacker.weaponTypeID + '/">' + attacker.weaponTypeName + '</a></td>' +
            '</tr>' +
            '<tr class="' + classText + '">' +
                '<td>Damage done: <span style="color: #e42f2f;"><b>' + format(Math.round(attacker.damageDone)) + '</b></span> (<span style="color: #3d9e2f; ">' + parseFloat((attacker.damageDone / totalDamage) * 100).toFixed(2) + '%</span>)</td>' +
            '</tr>';

            return h;
        };
        h += '<table class="kb-table" width="380" border="0" cellspacing="1">';
        data.attackers.forEach(function(attacker) {
            h +=
                '<tr class="kill-pilot-name">' +
                    '<td colspan="3">' +
                        '<a href="/character/' + attacker.characterID + '/">'+ attacker.characterName+'</a>' +
                    '</td>' +
                '</tr>' +
                '<col class="logo" />' +
                '<col class="logo" />' +
                '<col class="attribute-data" />';

            if(isOdd(attackerCnt)) {
                h += html(attacker, true);
            } else {
                h += html(attacker, false);
            }

            attackerCnt++;
        });

        h += "</table>";
        $("#InvolvedPartiesList").append(h);

    };

};