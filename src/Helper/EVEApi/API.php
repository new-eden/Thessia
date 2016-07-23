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

namespace Thessia\Helper\EVEApi;


use Thessia\Helper\Pheal;

/**
 * Class API
 * @package Thessia\Helper\EVEApi
 */
class API {
    /**
     * @var Pheal
     */
    private $pheal;

    /**
     * API constructor.
     * @param Pheal $pheal
     */
    public function __construct(Pheal $pheal) {
        $this->pheal = $pheal;
    }

    /**
     * Returns a list of the API Calls that contain private Character or Corporation information and which access bits are required.
     *
     * @return array
     */
    public function apiCallList(): array {
        try {
            $p = $this->pheal->Pheal();
            $p->scope = "API";
            $result = $p->CallList()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException(null, null, $exception);
            return array();
        }
    }
}