<?php

namespace etobi\extensionUtils\ConsoleHelper;

use Symfony\Component\Console\Helper\Helper;

/**
 * a simple manager to handle configuration
 * like getting, setting, overriding, bulk mode etc
 */
class ConfigHelper extends Helper {

    protected $config = array();

    public function set($name, $value = NULL) {
        if(is_string($name)) {
            $config = &$this->config;
            foreach($this->tokenizeName($name) as $token) {
                if(!array_key_exists($token, $config)) {
                    $config[$token] = array();
                    $config = &$config[$token];
                }
            }
            $config = $value;
        } else {
            throw new \InvalidArgumentException('$name must be a string or an array');
        }
    }

    public function get($name, $default = NULL) {
        $config = &$this->config;
        foreach($this->tokenizeName($name) as $token) {
            if(!array_key_exists($token, $config)) {
                return $default;
            }
            $config = &$config[$token];
        }
        return $config;
    }

    public function has($name) {
        $config = &$this->config;
        foreach($this->tokenizeName($name) as $token) {
            if(!array_key_exists($token, $config)) {
                return FALSE;
            }
            $config = &$config[$token];
        }
        return TRUE;
    }

    public function mergeConfiguration($configuration) {
        $this->config = $this->mergeRecursive($this->config, $configuration);
    }

    /**
     * Merges two arrays recursively where the values from the second array override the ones from the first one
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    protected function mergeRecursive($array1, $array2) {
        foreach ($array2 as $key => $value) {

            if (is_array($value) && array_key_exists($key, $array1) && is_array($array1[$key])) {
                $array1[$key] = $this->mergeRecursive($array1[$key], $value);
            } else {
                $array1[$key] = $value;
            }
        }

        return $array1;
    }

    /**
     * @param string $name
     * @return array
     */
    protected function tokenizeName($name) {
        $name = trim($name);
        return explode('.', $name);
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     *
     * @api
     */
    public function getName()
    {
        return 'config';
    }
}