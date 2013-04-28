<?php

namespace etobi\extensionUtils\Command;

use etobi\extensionUtils\Controller\T3xController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;
use etobi\extensionUtils\Service\T3xFile;

/**
 * CreateCommand creates a T3X file
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class CreateCommand extends AbstractCommand
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
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'force override if the file already exists')
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
        $t3xFile = $input->getArgument('t3xFile');
        if(file_exists($t3xFile) && !$this->shouldFileBeOverridden($t3xFile)) {
            $this->logger->notice('Aborting because file already exists');
            return 1;
        }

        $t3xFileService = new T3xFile();
        $t3xFileService->create(
            $input->getArgument('extensionKey'),
            $input->getArgument('sourcePath'),
            $t3xFile
        );
        return 0;
    }

    /**
     * if an existing file should be overridden
     *
     * uses console options or asks the user for permission
     *
     * @param $fileName
     * @return bool
     */
    protected function shouldFileBeOverridden($fileName) {
        if($this->input->getOption('force')) {
            return TRUE;
        }

        if(!$this->getHelperSet()->has('dialog')) {
            $this->logger->debug('DialogHelper is not enabled.');
            return FALSE;
        }
        /**
         * @var \Symfony\Component\Console\Helper\DialogHelper
         */
        $dialogHelper = $this->getHelperSet()->get('dialog');
        $this->output->writeln(sprintf('The file "%s" already exists', $fileName));
        return $dialogHelper->askConfirmation(
            $this->output,
            '<question>Override existing file? (y/N)</question>',
            false
        );
    }
}
