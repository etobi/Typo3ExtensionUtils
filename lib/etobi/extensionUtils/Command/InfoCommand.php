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
        $logger = new ConsoleOutputLoggerProxy($output);
        $controller = new TerController();
        $controller->setLogger($logger);
        $controller->infoAction(
            $input->getArgument('extensionKey'),
            $input->hasArgument('version') ? $input->getArgument('version') : NULL
        );
    }
}
