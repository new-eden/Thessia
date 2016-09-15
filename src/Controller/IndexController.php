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
    public function index($page = 1)
    {
        //@todo add information from the login data, if the person is logged in tha is..
        if($page == 1) {
            $menu = array(
                "Next Page" => $this->getFullHost() . "page/" . ($page + 1) . "/",
            );
        } else {
            $menu = array(
                "Previous Page" => $this->getFullHost() . "page/" . ($page - 1) . "/",
                "Next Page" => $this->getFullHost() . "page/" . ($page + 1) . "/",
            );
        }

        return $this->render("/pages/frontpage.twig", array("menu" => $menu));
    }

    private function getLoginData() {

    }

    public function kill($killID) {
        $menu = array(
            "Stuff" => "",
        );

        return $this->render("/pages/kill.twig", array("killID" => $killID, "menu" => $menu));
    }
}