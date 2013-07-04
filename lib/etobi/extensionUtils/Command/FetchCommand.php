<?php

namespace etobi\extensionUtils\Command;

use etobi\extensionUtils\Controller\TerController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * FetchCommand downloads an extension
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class FetchCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fetch')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'the extension you want to fetch'),
                new InputArgument('version', InputArgument::OPTIONAL, 'the version you want to fetch'),
                new InputArgument('destinationPath', InputArgument::OPTIONAL, 'the path to write the extension to'),
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'force override if the file already exists'),
                new InputOption('extract', 'x', InputOption::VALUE_NONE, 'extract the downloaded file'),
            ))
            ->setDescription('Download an extension')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionKey = $input->getArgument('extensionKey');
        $version = $input->getArgument('version');
        $destinationPath = $input->getArgument('destinationPath');
        if(!$version) {
            $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
            $version = $extensionsXmlService->findLatestVersion($extensionKey);
            if (!$version) {
                $this->logger->critical('could not find latest version of ' . $extensionKey);
                return 1;
            } else {
                $this->logger->info(sprintf('Latest version of %s is %s', $extensionKey, $version));
            }
        }
        if(!$destinationPath) {
            $destinationPath = $extensionKey . '_'. $version . '.t3x';
            $this->logger->info(sprintf('"%s" used as file name', $destinationPath));
        }

        if(file_exists($destinationPath) && !$this->shouldFileBeOverridden($destinationPath)) {
            $this->logger->notice('Aborting because file already exists');
            return 1;
        }

        $extensionService = new \etobi\extensionUtils\Service\Extension();
        $url = $extensionService->getDownloadUri($extensionKey, $version);

        $callback = $this->getProgressCallback();
        $downloader = new \etobi\extensionUtils\Service\Downloader();
        $downloader->downloadFile($url, $destinationPath, $callback);

        $this->logger->notice(sprintf('%s (%s) downloaded', $extensionKey, $version));

        if(!$this->input->getOption('extract')) {
            return 0;
        } else {
            $command = $this->getApplication()->find('t3x:extract');

            $arguments = array(
                'command'          => 't3x:extract',
                't3xFile'          => $destinationPath,
                'destinationPath'  => NULL,
                '--force'          => $input->getOption('force'),
            );

            $input = new ArrayInput($arguments);
            return $command->run($input, $output);
        }

    }
}
