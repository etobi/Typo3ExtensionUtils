<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Command\AbstractCommand;
use etobi\extensionUtils\Controller\TerController;

use etobi\extensionUtils\Service\Downloader;
use etobi\extensionUtils\Service\Extension;
use etobi\extensionUtils\Service\ExtensionsXml;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * FetchCommand downloads an extension
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class FetchCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:fetch')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'the extension you want to fetch'),
                new InputArgument('version', InputArgument::OPTIONAL, 'the version you want to fetch'),
                new InputArgument('destinationPath', InputArgument::OPTIONAL, 'the path to write the extension to'),
                new InputOption('force', 'f', InputOption::VALUE_NONE, 'force override if the file already exists'),
                new InputOption('extract', 'x', InputOption::VALUE_NONE, 'extract the downloaded file'),
            ))
            ->setDescription('Download an extension')
            ->setHelp(<<<EOT
Download an extension

Example
=======

Download the latest version of "my_extension" and store the t3x as my_extension_1.2.3.t3x

  t3xutils ter:fetch my_extension

Download extension "my_extension" in version 1.2.3, extract and store as my_extension

  t3xutils ter:fetch -x my_extension 1.2.3 my_extension
EOT
)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionKey = $input->getArgument('extensionKey');
        $version = $input->getArgument('version');
        $destinationPath = $input->getArgument('destinationPath');

	    $extensionsXmlService = new ExtensionsXml();
	    try {
		    $extensionsXmlService->isFileValid();
	    } catch(\InvalidArgumentException $e) {
		    $this->logger->info('Information file is not yet loaded. Fetch it.');

		    $command = $this->getApplication()->find('ter:update-info');
		    $arguments = array(
			    'command' => 'ter:update-info',
		    );
		    $updateInfoInput = new ArrayInput($arguments);
		    $command->run($updateInfoInput, $output);
	    }

        if(!$version) {
            $version = $extensionsXmlService->findLatestVersion($extensionKey);
            if (!$version) {
                $this->logger->critical('could not find latest version of ' . $extensionKey);
                return 1;
            } else {
                $this->logger->info(sprintf('Latest version of %s is %s', $extensionKey, $version));
            }
        }
        if(!$destinationPath) {
            $destinationPath = $extensionKey . '_'. $version . '.t3x';
            $this->logger->info(sprintf('"%s" used as file name', $destinationPath));
        }

        if(file_exists($destinationPath) && !$this->shouldFileBeOverridden($destinationPath)) {
            $this->logger->notice('Aborting because file already exists');
            return 1;
        }

        $extensionService = new Extension();
        $url = $extensionService->getDownloadUri($extensionKey, $version);

        $callback = $this->getProgressCallback();
        $downloader = new Downloader();
        $downloader->downloadFile($url, $destinationPath, $callback);

        $this->logger->notice(sprintf('%s (%s) downloaded', $extensionKey, $version));

        if(!$this->input->getOption('extract')) {
            return 0;
        } else {
            $command = $this->getApplication()->find('t3x:extract');

            $arguments = array(
                'command'          => 't3x:extract',
                't3xFile'          => $destinationPath,
                'destinationPath'  => NULL,
                '--force'          => $input->getOption('force'),
            );

            $input = new ArrayInput($arguments);
            return $command->run($input, $output);
        }

    }
}
