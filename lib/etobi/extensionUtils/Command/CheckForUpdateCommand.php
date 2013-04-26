<?php

namespace etobi\extensionUtils\Command;

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
class CheckForUpdateCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('checkforupdate')
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
        $controller = new SelfController();
        $controller->checkForUpdateAction();
    }
}
