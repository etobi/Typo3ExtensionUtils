<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;

/**
 * UploadCommand uploads an extension into TER
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class UploadCommand extends Command
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('upload')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Your username at typo3.org'),
                new InputArgument('password', InputArgument::REQUIRED, 'Your password at typo3.org'),
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'The extension key you want to upload an extension for'),
                new InputArgument('uploadComment', InputArgument::REQUIRED, 'Brief description what has changed with this version'),
                new InputArgument('pathToExtension', InputArgument::REQUIRED, 'the path to the extension on your local file system'),
            ))
            ->setDescription('Upload an extension to TER')
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
        $controller = new \etobi\extensionUtils\Controller\TerController();
        $controller->setLogger($logger);
        $success = $controller->uploadAction(
            $input->getArgument('username'),
            $input->getArgument('password'),
            $input->getArgument('extensionKey'),
            $input->getArgument('uploadComment'),
            $input->getArgument('pathToExtension')
        );
        return $success ? 0 : 1;
    }
}
