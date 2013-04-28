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
 * InfoCommand shows information on an extension
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class InfoCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('info')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'the extension key you want information on'),
                new InputArgument('version', InputArgument::OPTIONAL, 'get information on an specific version'),
                new InputOption('latest', 'l', InputOption::VALUE_NONE, 'get information on the latest version if non is specified'),
            ))
            ->setDescription('Get information about an extension')
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

        if(!$version && $input->getOption('latest')) {
            $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
            $version = $extensionsXmlService->findLatestVersion($extensionKey);
            $input->setArgument('version', $version);
            $this->logger->info(sprintf('Latest version of %s is %s', $extensionKey, $version));
        }

        if($version) {
            return $this->executeVersionInfo($input, $output);
        } else {
            return $this->executeExtensionInfo($input, $output);
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return integer
     */
    protected function executeExtensionInfo(InputInterface $input, OutputInterface $output) {
        $extensionKey = $input->getArgument('extensionKey');

        $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
        $versionInfos = $extensionsXmlService->getExtensionInfo($extensionKey);

        $output->writeln('Available versions:');
        foreach($versionInfos as $versionInfo) {
            $output->writeln(' ' .
                    $versionInfo['version'] .
                    '    uploaded: ' .
                    date('d.m.Y H:i:s', $versionInfo['timestamp'])
            // chr(10) .
            // $versionInfo['comment']
            );
        }

        return 0;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return integer
     */
    protected function executeVersionInfo(InputInterface $input, OutputInterface $output) {
        $extensionKey = $input->getArgument('extensionKey');
        $version = $input->getArgument('version');

        $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
        $infos = $extensionsXmlService->getVersionInfo($extensionKey, $version);

        $output->writeln('Extension: ' . $extensionKey . ' ' . $version);

        foreach ($infos as $key => $info) {
            if ($key === 'lastuploaddate') {
                $info = date('d.m.Y H:i:s', $info);
            } else if ($key === 'dependencies') {
                $info = var_export(unserialize($info), TRUE);
            }
            $output->writeln(' ' .
                    str_pad($key, 15, ' ', STR_PAD_RIGHT) .
                    '    ' .
                    $info
            );
        }

        return 0;
    }
}
