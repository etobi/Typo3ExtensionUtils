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
}
