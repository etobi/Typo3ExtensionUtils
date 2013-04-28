<?php

namespace etobi\extensionUtils\Command;

use etobi\extensionUtils\Controller\TerController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;

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
            $destinationPath = $extensionKey . '.t3x';
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

        return 0;
    }
}
