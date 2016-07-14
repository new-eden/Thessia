<?php
/**
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

namespace Thessia\Controller;

use Thessia\Middleware\Controller;
use Thessia\Model\CCP\typeIDs;
use Thessia\Model\EVE\Participants;

class IndexController extends Controller
{
    public function index()
    {
        $container = $this->getContainer();
        /** @var typeIDs $typeIDs */
        $typeIDs = $container->get("typeIDs");
        /** @var Participants $participants */
        $participants = $container->get("participants");

        $kills = $participants->getAllKills();
        foreach ($kills as $kill) {
            var_dump($kill);
        }

        //$data1 = $typeIDs->getAllByTypeID(638)->toArray();
        //$data2 = $typeIDs->getAllByName("Raven")->toArray();
        //var_dump($data1);
        //var_dump($data2);
        $this->render("index.html", array());
    }
}