<?php

namespace etobi\extensionUtils\Controller;

use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Helper\ProgressHelper;

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
     * @var null|OutputInterface
     */
    protected $output = null;

    /**
     * @var null|ProgressHelper
     */
    protected $progressHelper = null;

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

    /**
     * @param OutputInterface $ouput
     */
    public function setOutput(OutputInterface $ouput) {
        $this->output = $ouput;
    }

    /**
     * @param ProgressHelper $progressHelper
     */
    public function setProgressHelper(ProgressHelper $progressHelper) {
        $this->progressHelper = $progressHelper;
    }

}