<?php

namespace etobi\extensionUtils\Controller;

abstract class AbstractController {

    /**
     * a logger for informal information
     *
     * Everything logged should be completely optional and only informative or for debugging.
     * Errors should still be thrown and not be logged here.
     *
     * @var null|\Psr\Log\LoggerInterface
     */
    protected $logger = null;

    /**
     * @note The dependency on \Psr\Log\LoggerInterface should not be required so that
     *       this library can be used even without it
     *
     * @param $logger \Psr\Log\LoggerInterface
     * @throws \InvalidArgumentException
     */
    public function setLogger($logger) {
        if(is_null($logger)) {
            // noop
        } elseif(interface_exists('Psr\\Log\\LoggerInterface', true) && !($logger instanceof \Psr\Log\LoggerInterface)) {
            throw new \InvalidArgumentException('Psr\\Log\\LoggerInterface expected in ' . __CLASS__ . '::' . __FUNCTION__);
        } elseif(!is_object($logger)) {
            throw new \InvalidArgumentException('object expected in ' . __CLASS__ . '::' . __FUNCTION__);
        }
        $this->logger = $logger;
    }

}