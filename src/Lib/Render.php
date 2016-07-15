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

namespace Thessia\Lib;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Body;
use Slim\Views\Twig;
use XMLParser\XMLParser;

/**
 * Class Render
 * @package Rena\Lib
 */
class Render
{
    /**
     * @var Twig
     */
    private $view;
    private $response;
    private $request;

    /**
     * Render constructor.
     * @param Twig $twig
     * @param ResponseInterface $response
     * @param RequestInterface $request
     */
    public function __construct(Twig $twig, ResponseInterface $response, RequestInterface $request)
    {
        $this->view = $twig;
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * @param String $templateFile
     * @param array $dataArray
     * @param int|null $status
     * @param String|null $contentType
     * @return ResponseInterface
     */
    public function render(String $templateFile, $dataArray = array(), int $status = null, String $contentType = null)
    {
        $contentType = $contentType ?? $this->response->getHeader("Content-Type");

        // Run the scrapeCheck
        $this->scrapeChecker();

        if (stristr($contentType, "application/json")) {
            return $this->toJson($dataArray, $status, $this->response);
        }
        if (stristr($contentType, "application/xml")) {
            return $this->toXML($dataArray, $status, $this->response);
        }

        return $this->toTwig($templateFile, $dataArray, $status, $this->response);
    }

    /**
     *
     */
    private function scrapeChecker()
    {

    }

    /**
     * @param array $dataArray
     * @param int $status
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function toJson($dataArray = array(), int $status = 200, ResponseInterface $response)
    {
        /** @var ResponseInterface $resp */
        $resp = $response->withStatus($status)
            ->withHeader('Content-type', 'application/json; charset=utf-8')
            ->withAddedHeader("Access-Control-Allow-Origin", "*")
            ->withAddedHeader("Access-Control-Allow-Methods", "*");

        /** @var Body $body */
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write(json_encode($dataArray));

        return $resp->withBody($body);
    }

    /**
     * @param array $dataArray
     * @param int $status
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function toXML($dataArray = array(), int $status = 200, ResponseInterface $response)
    {
        /** @var ResponseInterface $resp */
        $resp = $response->withStatus($status)
            ->withHeader('Content-type', 'application/xml; charset=utf-8')
            ->withAddedHeader("Access-Control-Allow-Origin", "*")
            ->withAddedHeader("Access-Control-Allow-Methods", "*");

        /** @var XMLParser $xml */
        $xml = XMLParser::encode($dataArray, "Thessia");

        /** @var Body $body */
        $body = new Body(fopen('php://memory', 'r+'));
        $body->write($xml->asXML());

        return $resp->withBody($body);
    }


    /**
     * @param String $templateFile
     * @param array $dataArray
     * @param int $status
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    private function toTwig(String $templateFile, $dataArray = array(), int $status = 200, ResponseInterface $response)
    {
        // Get all the character information on the guy who is logged in
        $extraData = array();

        // Merge the arrays
        $dataArray = array_merge($extraData, $dataArray);

        /** @var ResponseInterface $resp */
        $resp = $response->withStatus($status)
            ->withHeader("Content-Type", "text/html; charset=utf-8");

        /** @var Body $body */
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($this->view->fetch($templateFile, $dataArray));

        return $resp->withBody($body);

    }
}