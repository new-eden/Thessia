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

/**
 * Created by PhpStorm.
 * User: micha
 * Date: 15-08-2016
 * Time: 13:58
 */

namespace Thessia\Controller\API;


use Slim\App;
use Thessia\Middleware\Controller;

class WarsAPIController extends Controller {
    private $collection;
    private $killmails;
    private $cache;

    public function __construct(App $app) {
        parent::__construct($app);
        $this->cache = $this->container->get("cache");
        $this->collection = $this->mongo->selectCollection("thessia", "wars");
        $this->killmails = $this->mongo->selectCollection("thessia", "killmails");
    }

    public function count() {
        return $this->json(array("warCount" => $this->collection->count()));
    }

    public function wars(int $warID = null) {
        if($warID > 0)
            return $this->json($this->collection->find(array("warID" => $warID))->toArray());
        return $this->json($this->collection->find()->toArray());
    }

    public function warMails(int $warID = null, int $page = 1) {
        $limit = 100;
        $offset = $limit * ($page - 1);

        if(!$warID || $warID == 0)
            return $this->json(array("Error" => "No warID selected..."));

        return $this->json($this->killmails->find(array("warID" => $warID), array("offset" => $offset, "limit" => $limit))->toArray());
    }

}