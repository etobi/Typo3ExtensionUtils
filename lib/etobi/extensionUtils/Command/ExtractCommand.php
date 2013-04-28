<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;
use etobi\extensionUtils\Service\T3xFile;

/**
 * ExtractCommand extracts a T3X file
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class ExtractCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('extract')
            ->setDefinition(array(
                new InputArgument('t3xFile', InputArgument::REQUIRED, 'path to t3x file'),
                new InputArgument('destinationPath', InputArgument::REQUIRED, 'path of to unpack the extension to'),
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'force override if the folder already exists'),
            ))
            ->setDescription('Extract a t3x file')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destinationPath = $input->getArgument('destinationPath');
        if(file_exists($destinationPath)) {
            if($this->shouldFolderBeOverridden($destinationPath)) {
                if($this->deleteDirectory($destinationPath)) {
                    $this->logger->debug(sprintf('"%s" removed', $destinationPath));
                } else {
                    $this->logger->critical(sprintf('Could not remove directory "%s"', $destinationPath));
                    return 1;
                }
            } else {
                $this->logger->notice('Aborting because folder already exists');
                return 1;
            }
        }

        $t3xFileService = new T3xFile();
        $t3xFileService->extract(
            $input->getArgument('t3xFile'),
            $destinationPath
        );

        return 0;
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
        if($this->input->getOption('force')) {
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
