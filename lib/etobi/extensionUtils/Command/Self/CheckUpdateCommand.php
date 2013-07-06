<?php

namespace etobi\extensionUtils\Command\Self;

use etobi\extensionUtils\Command\AbstractCommand;
use etobi\extensionUtils\ConsoleUtility\ConsoleOutputLoggerProxy;
use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CheckForUpdateCommand checks if an update of t3xutils is available
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class CheckUpdateCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self:check-update')
            ->setDefinition(array())
            ->setDescription('Check if an update for t3xutils is available')
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
        $controller = new SelfController();
        $controller->setLogger($logger);
        $controller->checkForUpdateAction();
    }
}
