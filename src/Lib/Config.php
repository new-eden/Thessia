<?php
namespace Thessia\Lib;

class Config
{
    private $config;

    public function __construct($configFile)
    {
        $this->loadConfig($configFile);
    }

    public function loadConfig($configFile)
    {
        if (!file_exists(realpath($configFile))) {
            return;
        }
        $this->config = array_change_key_case(include($configFile), \CASE_LOWER);
    }

    public function get($key, $type = null, $default = null)
    {
        $type = strtolower($type);

        if (!empty($this->config[$type][$key])) {
            return $this->config[$type][$key];
        }

        return $default;
    }

    public function getAll($type = null)
    {
        $type = strtolower($type);

        if (!empty($this->config[$type])) {
            return $this->config[$type];
        }

        return array();
    }
}