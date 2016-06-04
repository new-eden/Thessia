<?php
namespace Thessia\Lib;

use Closure;
use Redis;

class Cache
{
    public $persistence = true;
    private $redis;

    function __construct(Config $config)
    {
        $this->redis = new Redis();
        if (!$this->persistence) {
                    $this->redis->connect($config->get("host", "redis", "127.0.0.1"), $config->get("port", "redis", 6379));
        } else {
                    $this->redis->pconnect($config->get("host", "redis", "127.0.0.1"), $config->get("port", "redis", 6379));
        }
    }

    /**
     * Returns the redis handle for usage in places where the Cache functions aren't enough
     *
     * @return Redis
     */
    public function returnRedis()
    {
        return $this->redis;
    }

    /**
     * Read value from the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }

    /**
     * Write value to the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     * @param string $value The value to be cached
     * @param integer $timeout .
     *
     * @return bool
     */
    public function set($key, $value, $timeout = 0)
    {
        $result = $this->redis->set($key, $value);
        if ($timeout > 0) {
            return $result ? $this->expire($key, $timeout) : $result;
        }
        return $result;
    }

    /**
     * Sets expiration time for Cache key.
     *
     * @param string $key The key to uniquely identify the cached item
     * @param integer $timeout
     *
     * @return bool
     */
    protected function expire($key, $timeout)
    {
        return $this->redis->expire($key, $timeout);
    }

    /**
     * Override value in the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     * @param mixed $value The value to be cached
     * @param null|string $timeout A strtotime() compatible Cache time.
     *
     * @return bool
     */
    public function replace($key, $value, $timeout)
    {
        return $this->redis->set($key, $value, $timeout);
    }

    /**
     * Delete value from the Cache.
     *
     * @param string $key The key to uniquely identify the cached item
     *
     * @return bool
     */
    public function delete($key)
    {
        return (boolean)$this->redis->del($key);
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
     * @return callable Function returning item's new value on successful increment, else `false`
     */
    public function increment($key, $timeout = 0)
    {
        $data = $this->redis->incr($key);
        if ($timeout) {
            $this->expire($key, $timeout);
        }
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
     * @return Closure Function returning item's new value on successful decrement, else `false`
     */
    public function decrement($key, $timeout = 0)
    {
        $data = $this->redis->decr($key);
        if ($timeout) {
            $this->expire($key, $timeout);
        }
        return $data;
    }

    /**
     * Clears user-space Cache.
     *
     * @return bool|null
     */
    public function flush()
    {
        $this->redis->flushDB();
    }
}