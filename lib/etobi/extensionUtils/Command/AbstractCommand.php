<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;
use Psr\Log\LoggerInterface;
use etobi\extensionUtils\Service\FileSizeProgressBar;

/**
 * UpdateInfoCommand updates the extension information
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
abstract class AbstractCommand extends Command
{

    /**
     * This will be a valid LoggerInterface in the execute() method
     *
     * @var null|LoggerInterface
     */
    protected $logger = null;

    /**
     * This will be a valid OutputInterface in the execute() method
     *
     * @var null|OutputInterface
     */
    protected $output = null;

    /**
     * This will be a valid InputInterface in the execute() method
     *
     * @var null|InputInterface
     */
    protected $input = null;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger = null) {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function initialize(InputInterface $input, OutputInterface $output) {
        if(!$this->logger) {
            $this->logger = new ConsoleOutputLoggerProxy($output);
        }
        $this->output = $output;
        $this->input = $input;
    }

    /**
     * @return null|\Symfony\Component\Console\Helper\ProgressHelper
     */
    protected function getProgressHelper() {
        if(!$this->getHelperSet()->has('progress')) {
            return null;
        }

        return $this->getHelperSet()->get('progress');
    }

    /**
     * get a callback that can be used in CURL as a progress callback
     *
     * @return callback|null
     */
    protected function getProgressCallback() {
        $progressHelper = $this->getProgressHelper();
        if(!$progressHelper) {
            $this->logger->debug('The progress helper is not enabled on this console');
            return null;
        }

        if(!defined('CURLOPT_PROGRESSFUNCTION')) {
            $this->logger->debug('Progress bar is only supported from PHP 5.3 on');
            return NULL;
        }

        $progressBar = new FileSizeProgressBar($progressHelper, $this->output);
        return array($progressBar, 'progressCallback');
    }

}
