<?php

namespace etobi\extensionUtils\Command;

use etobi\extensionUtils\Controller\T3xController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;

/**
 * ExtractCommand extracts a T3X file
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class ExtractCommand extends Command
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
        $logger = new ConsoleOutputLoggerProxy($output);
        $controller = new T3xController();
        $controller->setLogger($logger);
        $controller->extractAction(
            $input->getArgument('t3xFile'),
            $input->getArgument('destinationPath')
        );
    }
}
