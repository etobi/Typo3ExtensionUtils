<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\ConsoleUtility\ConsoleOutputLoggerProxy;
use Psr\Log\LoggerInterface;
use etobi\extensionUtils\ConsoleUtility\FileSizeProgressBar;

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

    /**
     * @author <erkethan at free dot fr>
     * @see http://php.net/manual/de/function.rmdir.php#92050
     * @param $dir
     * @return bool
     */
    protected function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                chmod($dir . DIRECTORY_SEPARATOR . $item, 0777);
                if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
            };
        }
        return rmdir($dir);
    }

}
