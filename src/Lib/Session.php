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

    function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Close the session.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.close.php
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
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
     * @param int $session_id The session ID being destroyed.
     *
     * @return boolean|null <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function destroy($session_id)
    {
        $this->cache->delete($session_id);
    }

    /**
     * PHP >= 5.4.0<br/>
     * Cleanup old sessions.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
     *
     * @param int $maxlifetime <p>
     *                         Sessions that have not updated for
     *                         the last maxlifetime seconds will be removed.
     *                         </p>
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Initialize session.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.open.php
     *
     * @param string $save_path The path where to store/retrieve the session.
     * @param string $session_id The session id.
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function open($save_path, $session_id)
    {
        return true;
    }

    /**
     * PHP >= 5.4.0<br/>
     * Read session data.
     *
     * @link http://php.net/manual/en/sessionhandlerinterface.read.php
     *
     * @param string $session_id The session id to read data for.
     *
     * @return string <p>
     *                Returns an encoded string of the read data.
     *                If nothing was read, it must return an empty string.
     *                Note this value is returned internally to PHP for processing.
     *                </p>
     */
    public function read($session_id)
    {
        $data = $this->cache->get($session_id);
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
     * @param string $session_id The session id.
     * @param string $session_data <p>
     *                             The encoded session data. This data is the
     *                             result of the PHP internally encoding
     *                             the $_SESSION superglobal to a serialized
     *                             string and passing it as this parameter.
     *                             Please note sessions use an alternative serialization method.
     *                             </p>
     *
     * @return bool <p>
     *              The return value (usually TRUE on success, FALSE on failure).
     *              Note this value is returned internally to PHP for processing.
     *              </p>
     */
    public function write($session_id, $session_data)
    {
        $this->cache->set($session_id, $session_data, $this->ttl);
        return true;
    }

    public function RunAsNew()
    {
    }
}