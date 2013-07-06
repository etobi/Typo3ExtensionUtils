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
 * SelfUpdateCommand updates t3xutils
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class UpdateCommand extends AbstractCommand
{
    
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('self:update')
            ->setDefinition(array())
            ->setDescription('Update t3xutils')
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
        $controller->setLogger($output);
        $controller->updateAction();
    }
}
