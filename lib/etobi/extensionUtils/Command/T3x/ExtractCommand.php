<?php

namespace etobi\extensionUtils\Command\T3x;

use etobi\extensionUtils\Command\AbstractCommand;
use etobi\extensionUtils\Service\Extension;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Service\T3xFile;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ExtractCommand extracts a T3X file
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class ExtractCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('t3x:extract')
            ->setDefinition(array(
                new InputArgument('t3xFile', InputArgument::REQUIRED, 'path to t3x file'),
                new InputArgument('destinationPath', InputArgument::OPTIONAL, 'path of to unpack the extension to'),
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'force override if the folder already exists'),
            ))
            ->setDescription('Extract a t3x file')
            ->setHelp(<<<EOT
Extract a t3x file into a local folder

Example
=======

Extract my_extension.t3x to the folder "my_extension/"

  t3xutils t3x:extract my_extension.t3x

Create or update the folder "my_extension" by extracting "latest.t3x"

  t3xutils t3x:extract -f latest.t3x my_extension/
EOT
)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destinationPath = $input->getArgument('destinationPath');
        $t3xFile = $input->getArgument('t3xFile');
        if(!$destinationPath) {
            $extensionService = new Extension();
            $destinationPath = $extensionService->getExtensionNameFromFileName($t3xFile);
            $this->logger->info(sprintf('"%s" used as folder name', $destinationPath));
        }
        if(file_exists($destinationPath)) {
            if($this->shouldFolderBeOverridden($destinationPath)) {
                $filesystemService = new Filesystem();
                $filesystemService->remove($destinationPath);
                $this->logger->debug(sprintf('"%s" removed', $destinationPath));
            } else {
                $this->logger->notice('Aborting because folder already exists');
                return 1;
            }
        }

        $t3xFileService = new T3xFile();
        $success = $t3xFileService->extract(
            $t3xFile,
            $destinationPath
        );

        if($success) {
            $this->logger->notice(sprintf('"%s" extracted', $destinationPath));
            return 0;
        } else {
            $this->logger->critical('extension was not extracted');
            return 1;
        }
    }
}
