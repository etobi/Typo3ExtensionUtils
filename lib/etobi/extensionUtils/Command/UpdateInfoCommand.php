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
 * UpdateInfoCommand updates the extension information
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class UpdateInfoCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('updateinfo')
            ->setDefinition(array())
            ->setDescription('Update local extension information cache')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = 'http://typo3.org/fileadmin/ter/extensions.xml.gz';
        $extensionsXmlFile = '/tmp/t3xutils.extensions.temp.xml';
        $extensionsXmlFileGzipped = $extensionsXmlFile . '.gz';

        $this->logger->info('fetch extension info from "' . $url .'"');

        $callback = $this->getProgressCallback();
        $downloader = new \etobi\extensionUtils\Service\Downloader();
        $downloader->downloadFile($url, $extensionsXmlFileGzipped, $callback);

        $this->logger->info(sprintf('unpacking "%s"...', $extensionsXmlFileGzipped));

        $cmd = sprintf('gzip -df %s > %s', escapeshellarg($extensionsXmlFileGzipped), escapeshellarg($extensionsXmlFile));
        $this->logger->debug('executing: ' . $cmd);
        $cmdReturn = system($cmd);
        if($cmdReturn === FALSE) {
            $this->logger->critical(sprintf(
                'Unpacking %s failed.'
            ));
            return 1;
        } else {
            $this->logger->notice('extension info updated');
        }
        return 0;
    }
}
