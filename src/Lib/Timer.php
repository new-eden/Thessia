<?php
namespace Thessia\Lib;

class Timer
{
    /**
     * @var int containing the time the entire thing was started.
     */
    protected $startTime;

    /**
     * Starts the timer class.
     */
    public function __construct()
    {
        $this->start();
    }

    /**
     * Starts the timer.
     */
    public function start()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Stops the timer.
     *
     * @return double
     */
    public function stop()
    {
        return 1000 * (microtime(true) - $this->startTime);
    }

    /**
     *
     */
    public function RunAsNew()
    {
    }
}