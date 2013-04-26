<?php

namespace etobi\extensionUtils\Command;

use etobi\extensionUtils\Controller\T3xController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreateCommand creates a T3X file
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class CreateCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('create')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'extension key'),
                new InputArgument('sourcePath', InputArgument::REQUIRED, 'path of the extension'),
                new InputArgument('t3xFile', InputArgument::REQUIRED, 'filename and path to store the t3x file'),
            ))
            ->setDescription('Create a t3x file')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $controller = new T3xController();
        $controller->createAction(
            $input->getArgument('extensionKey'),
            $input->getArgument('sourcePath'),
            $input->getArgument('t3xFile')
        );
    }
}
