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

namespace Thessia\Controller\API;

use Slim\App;
use Thessia\Middleware\Controller;

class ImageAPIController extends Controller
{
    private $ccpImageServer = "https://image.eveonline.com";
    private $ccpImageCacheTime = 86400;
    private $imageCacheDir = __DIR__ . "/../../../cache/images";
    private $curl;

    /**
     * AllianceAPIController constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->curl = $this->container->get("curl");
    }

    public function getAllianceImage($allianceID, $size) {
        $filePath = "{$this->imageCacheDir}/alliance_{$allianceID}_{$size}.png";

        // Check if the image exists, and make sure the image age is less than 24hours old. If it is, we'll return the cached image instead
        if (file_exists($filePath) && ((time() - filemtime($filePath)) <= $this->ccpImageCacheTime))
            return $this->image(file_get_contents($filePath), $this->ccpImageCacheTime, 200, "image/png");

        // Get image from CCP
        $image = $this->curl->getData("{$this->ccpImageServer}/Alliance/{$allianceID}_{$size}.png");

        // Put the image into the filesystem cache
        file_put_contents($filePath, $image);

        // Return the image
        return $this->image($image, $this->ccpImageCacheTime, 200, "image/png");
    }

    public function getCorporationImage($corporationID, $size) {
        $filePath = "{$this->imageCacheDir}/corporation_{$corporationID}_{$size}.png";

        // Check if the image exists, and make sure the image age is less than 24hours old. If it is, we'll return the cached image instead
        if (file_exists($filePath) && ((time() - filemtime($filePath)) <= $this->ccpImageCacheTime))
            return $this->image(file_get_contents($filePath), $this->ccpImageCacheTime, 200, "image/png");

        // Get image from CCP
        $image = $this->curl->getData("{$this->ccpImageServer}/Corporation/{$corporationID}_{$size}.png");

        // Put the image into the filesystem cache
        file_put_contents($filePath, $image);

        // Return the image
        return $this->image($image, $this->ccpImageCacheTime, 200, "image/png");
    }

    public function getCharacterImage($characterID, $size) {
        $filePath = "{$this->imageCacheDir}/character_{$characterID}_{$size}.jpg";

        // Check if the image exists, and make sure the image age is less than 24hours old. If it is, we'll return the cached image instead
        if (file_exists($filePath) && ((time() - filemtime($filePath)) <= $this->ccpImageCacheTime))
            return $this->image(file_get_contents($filePath), $this->ccpImageCacheTime, 200, "image/jpg");

        // Get image from CCP
        $image = $this->curl->getData("{$this->ccpImageServer}/Character/{$characterID}_{$size}.jpg");

        // Put the image into the filesystem cache
        file_put_contents($filePath, $image);

        // Return the image
        return $this->image($image, $this->ccpImageCacheTime, 200, "image/jpg");
    }

    public function getInventoryImage($itemID, $size) {
        $filePath = "{$this->imageCacheDir}/inventory_{$itemID}_{$size}.png";

        // Check if the image exists, and make sure the image age is less than 24hours old. If it is, we'll return the cached image instead
        if (file_exists($filePath) && ((time() - filemtime($filePath)) <= $this->ccpImageCacheTime))
            return $this->image(file_get_contents($filePath), $this->ccpImageCacheTime, 200, "image/png");

        // Get image from CCP
        $image = $this->curl->getData("{$this->ccpImageServer}/Type/{$itemID}_{$size}.png");

        // Put the image into the filesystem cache
        file_put_contents($filePath, $image);

        // Return the image
        return $this->image($image, $this->ccpImageCacheTime, 200, "image/png");
    }

    public function getShipImage($shipTypeID, $size) {
        $filePath = "{$this->imageCacheDir}/ship_{$shipTypeID}_{$size}.png";

        // Check if the image exists, and make sure the image age is less than 24hours old. If it is, we'll return the cached image instead
        if (file_exists($filePath) && ((time() - filemtime($filePath)) <= $this->ccpImageCacheTime))
            return $this->image(file_get_contents($filePath), $this->ccpImageCacheTime, 200, "image/png");

        // Get image from CCP
        $image = $this->curl->getData("{$this->ccpImageServer}/Render/{$shipTypeID}_{$size}.png");

        // Put the image into the filesystem cache
        file_put_contents($filePath, $image);

        // Return the image
        return $this->image($image, $this->ccpImageCacheTime, 200, "image/png");
    }
}