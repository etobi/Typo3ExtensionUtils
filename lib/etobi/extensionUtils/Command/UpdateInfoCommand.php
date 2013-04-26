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
class UpdateInfoCommand extends Command
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
        $logger = new ConsoleOutputLoggerProxy($output);
        $controller = new TerController();
        $controller->setLogger($logger);
        $success = $controller->updateAction();

        return $success ? 0 : 1;
    }
}
