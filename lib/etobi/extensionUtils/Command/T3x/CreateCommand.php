<?php

namespace etobi\extensionUtils\Command\T3x;

use etobi\extensionUtils\Command\AbstractCommand;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
            ->setName('t3x:create')
            ->setDefinition(array(
		        new InputArgument('sourcePath', InputArgument::REQUIRED, 'path of the extension'),
		        new InputArgument('t3xFile', InputArgument::OPTIONAL, 'filename and path to store the t3x file'),
                new InputOption('extensionKey', NULL, InputOption::VALUE_REQUIRED, 'extension key'),
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'force override if the file already exists')
            ))
            ->setDescription('Create a t3x file')
            ->setHelp(<<<EOT
Create a t3x file from a local folder

Example
=======

Create a t3x for extension "my_extension" from the contents of the "my_extension/"
folder and store the content as my_extension.t3x

  t3xutils t3x:create my_extension/

Create or override the file "latest.t3x" with the contents of the "dev/" folder

  t3xutils t3x:create -f --extensionKey="my_extension" dev/ latest.t3x
EOT
)
        ;
    }

	protected function prepareParameters(InputInterface $input, OutputInterface $output) {
		$path = $input->getArgument('sourcePath');
		if(!file_exists($path)) {
			throw new \InvalidArgumentException(sprintf(
				'folder "%s" does not exist',
				$path
			));
		} elseif(!is_dir($path)) {
			throw new \InvalidArgumentException(sprintf(
				'"%s" is not a directory',
				$path
			));
		}
		if(!$input->getArgument('t3xFile')) {
			$t3xFile = basename($path);
			if(empty($t3xFile)) {
				$t3xFile = basename(getcwd());
			}
			$t3xFile .= '.t3x';
			$input->setArgument('t3xFile', $t3xFile);
			$this->logger->info(sprintf(
				'No t3x file given. Using %s.',
				$t3xFile
			));
		}
		if(!$input->getOption('extensionKey')) {
			$extensionKey = basename($path);
			if(empty($extensionKey)) {
				$extensionKey = basename(getcwd());
			}
			$input->setOption('extensionKey', $extensionKey);
			$this->logger->info(sprintf(
				'No extensionKey given. Asuming %s.',
				$extensionKey
			));
		}
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
        $success = $t3xFileService->create(
            $input->getOption('extensionKey'),
            $input->getArgument('sourcePath'),
            $t3xFile
        );

        if($success) {
            $this->logger->notice(sprintf('"%s" created', $t3xFile));
            return 0;
        } else {
            $this->logger->critical('t3x file was not created');
            return 1;
        }
    }
}
