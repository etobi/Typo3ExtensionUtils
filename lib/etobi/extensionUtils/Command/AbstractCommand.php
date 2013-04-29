<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\ConsoleUtility\ConsoleOutputLoggerProxy;
use Psr\Log\LoggerInterface;
use etobi\extensionUtils\ConsoleUtility\FileSizeProgressBar;

/**
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

        $this->prepareParameters($input, $output);
    }

    /**
     * prepare arguments and options before the command is run, use defaults or ask the user
     *
     * This method can be overridden to ask the user for missing
     * data if he did not enter an argument.
     * The method should be purely used for convenience for the user,
     * it should not contain any business logic.
     * For instance:
     *   * asking the user for a path to store some file is ok,
     *     if there was an option for it in the console command, but he did not fill it
     *   * asking if the given folder should be deleted if it exists should be done in the execute() method
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     */
    protected function prepareParameters(InputInterface $input, OutputInterface $output) {
        // noop
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
     * @throws \RuntimeException
     * @return null|\Symfony\Component\Console\Helper\DialogHelper
     */
    protected function getDialogHelper() {
        if(!$this->getHelperSet()->has('dialog')) {
            throw new \RuntimeException('DialogHelper is not enabled.');
        }

        return $this->getHelperSet()->get('dialog');
    }

    /**
     * get a configuration value a user supplied in a configuration file
     *
     * @param $name
     * @param null $default
     * @return null
     */
    protected function getConfigurationValue($name, $default = NULL) {
        if(!$this->getHelperSet()->has('config')) {
            return $default;
        }
        return $this->getHelperSet()->get('config')->get($name, $default);
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

    /**
     * if an existing file should be overridden
     *
     * uses console options or asks the user for permission
     *
     * @param $fileName
     * @return bool
     */
    protected function shouldFileBeOverridden($fileName) {
        if($this->input->hasOption('force') && $this->input->getOption('force')) {
            return TRUE;
        }

        if(!$this->getHelperSet()->has('dialog')) {
            $this->logger->debug('DialogHelper is not enabled.');
            return FALSE;
        }
        /**
         * @var \Symfony\Component\Console\Helper\DialogHelper
         */
        $dialogHelper = $this->getHelperSet()->get('dialog');
        $this->output->writeln(sprintf('The file "%s" already exists', $fileName));
        return $dialogHelper->askConfirmation(
            $this->output,
            '<question>Override existing file? (y/N)</question>',
            false
        );
    }

    /**
     * if an existing file should be overridden
     *
     * uses console options or asks the user for permission
     *
     * @param $folderName
     * @return bool
     */
    protected function shouldFolderBeOverridden($folderName) {
        if($this->input->hasOption('force') && $this->input->getOption('force')) {
            return TRUE;
        }

        if(!$this->getHelperSet()->has('dialog')) {
            $this->logger->debug('DialogHelper is not enabled.');
            return FALSE;
        }
        /**
         * @var \Symfony\Component\Console\Helper\DialogHelper
         */
        $dialogHelper = $this->getHelperSet()->get('dialog');
        $this->output->writeln(sprintf('The folder "%s" already exists', $folderName));
        return $dialogHelper->askConfirmation(
            $this->output,
            '<question>Override existing folder? (y/N)</question>',
            false
        );
    }
}
