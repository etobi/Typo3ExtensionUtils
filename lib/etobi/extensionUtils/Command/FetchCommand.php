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
class FetchCommand extends Command
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
        $logger = new ConsoleOutputLoggerProxy($output);
        $controller = new TerController();
        $controller->setLogger($logger);
        $controller->fetchAction(
            $input->getArgument('extensionKey'),
            $input->hasArgument('version') ? $input->getArgument('version') : NULL,
            $input->hasArgument('destinationPath') ? $input->getArgument('destinationPath') : NULL
        );
    }
}
