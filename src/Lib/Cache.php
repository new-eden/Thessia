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

//use Predis\Client;

/**
 * Class Cache
 * @package Thessia\Lib
 */
class Cache
{
    /**
     * @var bool
     */
    public $persistence = true;
    /**
     * @var \Memcached
     */
    private $cache;

    /**
     * Cache constructor.
     * @param Config $config
     */
    function __construct(Config $config)
    {
        $this->cache = new \Memcached("thessia");
        $this->cache->addServer("127.0.0.1", 11211, 1);
    }

    /**
     * Read value from the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return json_decode($this->cache->get($key), true);
    }

    /**
     * Write value to the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     * @param string $value The value to be cached
     * @param integer $timeout
     * @return bool
     */
    public function set(string $key, $value, int $timeout = 0)
    {
        $result = $this->cache->set($key, json_encode($value), $timeout);
        return $result;
    }

    /**
     * Returns true if a key exists. False if it doesn't
     *
     * @param string $key
     * @return bool
     */
    public function exists(string $key): bool {
        $data = $this->cache->get($key);
        if(!empty($data))
            return true;
        return false;
    }

    /**
     * Override value in the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     * @param mixed $value The value to be cached
     * @param int $timeout
     *
     * @return bool
     */
    public function replace(string $key, string $value, int $timeout): bool
    {
        return $this->cache->set($key, json_encode($value), $timeout);
    }

    /**
     * Delete value from the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     *
     * @return bool
     */
    public function delete(string $key): bool
    {
        return (boolean)$this->cache->delete($key);
    }

    /**
     * Performs an atomic increment operation on specified numeric Cache item.
     *
     * Note that if the value of the specified key is *not* an integer, the increment
     * operation will have no effect whatsoever. Redis chooses to not typecast values
     * to integers when performing an atomic increment operation.
     *
     * @param string $key Key of numeric Cache item to increment
     * @param int $timeout
     *
     * @return int|false Function returning item's new value on successful increment, else `false`
     */
    public function increment(string $key, int $timeout = 0): int
    {
        $data = $this->cache->increment($key, 1, 0, $timeout);
        return $data;
    }

    /**
     * Performs an atomic decrement operation on specified numeric Cache item.
     *
     * Note that if the value of the specified key is *not* an integer, the decrement
     * operation will have no effect whatsoever. Redis chooses to not typecast values
     * to integers when performing an atomic decrement operation.
     *
     * @param string $key Key of numeric Cache item to decrement
     * @param int $timeout A strtotime() compatible Cache time.
     *
     * @return int|false Function returning item's new value on successful decrement, else `false`
     */
    public function decrement(string $key, int $timeout = 0): int
    {
        $data = $this->cache->decrement($key, 1, 0, $timeout);
        return $data;
    }

    /**
     * Clears user-space Cache.
     *
     * @return bool
     */
    public function flush(): bool
    {
        return $this->cache->flush();
    }
}