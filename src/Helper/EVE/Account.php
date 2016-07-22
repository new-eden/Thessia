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

namespace Thessia\Helper\EVE;


use Thessia\Helper\Pheal;

class Account {
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
     * Returns basic account information including when the subscription lapses, total play time in minutes, total times logged on and date of account creation. In the case of game time code accounts it will also look for available offers of time codes.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function accountAPIKeyInfo(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Account";
            $result = $p->APIKeyInfo()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, null, $exception);
            return array();
        }
    }

    /**
     * Returns information about the API key and a list of the characters exposed by it.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function accountAccountStatus(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Account";
            $result = $p->accountStatus()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, null, $exception);
            return array();
        }
    }

    /**
     * Returns a list of all characters on an account.
     *
     * @param int $apiKey
     * @param string $vCode
     * @return array
     */
    public function accountCharacters(int $apiKey, string $vCode): array {
        try {
            $p = $this->pheal->Pheal($apiKey, $vCode);
            $p->scope = "Account";
            $result = $p->Characters()->toArray();
            return $result;
        } catch(\Exception $exception) {
            $this->pheal->handleApiException($apiKey, null, $exception);
            return array();
        }
    }
}