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

use SessionHandlerInterface;

/**
 * Class SessionHandler.
 */
class SessionHandler implements SessionHandlerInterface
{
    /**
     * @var int
     */
    protected $ttl = 7200; // 2hrs of cache time
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Close the session.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     *
     * @return bool
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     */
    public function close()
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Destroy a session.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
     *
     * @param int $sessionID The session ID being destroyed.
     *
     * @return boolean|null
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     */
    public function destroy($sessionID)
    {
        $this->cache->delete($sessionID);
    }

    /**
     * PHP >= 5.4.0<br/>
     * Cleanup old sessions.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     *
     * @param int $maxLifeTime
     *                         Sessions that have not updated for
     *                         the last maxLifeTime seconds will be removed.
     * @return bool
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     */
    public function gc($maxLifeTime)
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Initialize session.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     *
     * @param string $savePath The path where to store/retrieve the session.
     * @param string $sessionID The session id.
     *
     * @return bool
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     */
    public function open($savePath, $sessionID)
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Read session data.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     *
     * @param string $sessionID The session id to read data for.
     *
     * @return string
     *                Returns an encoded string of the read data.
     *                If nothing was read, it must return an empty string.
     *                Note this value is returned internally to PHP for processing.
     */
    public function read($sessionID)
    {
        $data = $this->cache->get($sessionID);
        if (!is_array($data)) {
            return (string)$data;
        }
        return ''; // Return string if the above is an array..
    }

    /**
     * PHP >= 5.4.0<br/>
     * Write session data.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.write.php
     *
     * @param string $sessionID The session id.
     * @param string $sessionData
     *                             The encoded session data. This data is the
     *                             result of the PHP internally encoding
     *                             the $_SESSION superglobal to a serialized
     *                             string and passing it as this parameter.
     *                             Please note sessions use an alternative serialization method.
     *
     * @return bool
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     */
    public function write($sessionID, $sessionData)
    {
        $this->cache->set($sessionID, $sessionData, $this->ttl);
        return true;
    }
}