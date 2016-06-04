<?php
namespace Thessia\Lib;

use Monolog\Logger;

class Config {
    private $config;
    private $logger;

    public function __construct($configFile, Logger $logger) {
        $this->logger = $logger;
        $this->loadConfig($configFile);
    }

    public function loadConfig($configFile) {
        if(!file_exists(realpath($configFile))) {
            $this->logger->addError("Config file " . realpath($configFile) . " not found..");
            return;
        }

        try {
            $this->config = array_change_key_case(include($configFile), \CASE_LOWER);
            $this->logger->addDebug("Config file loaded: " . realpath($configFile));
        } catch (\Exception $e) {
            $this->logger->addError("Failed to load config file (" . realpath($configFile) . "): " . $e->getMessage());
        }
    }

    public function get($key, $type = null, $default = null) {
        $type = strtolower($type);

        if (!empty($this->config[$type][$key])) {
            return $this->config[$type][$key];
        }

        $this->logger->addWarning("Config setting not found: {$type} / {$key}");

        return $default;
    }

    public function getAll($type = null) {
        $type = strtolower($type);

        if (!empty($this->config[$type])) {
            return $this->config[$type];
        }
        
        $this->logger->addWarning("Config group not found: {$type}");

        return array();
    }
}