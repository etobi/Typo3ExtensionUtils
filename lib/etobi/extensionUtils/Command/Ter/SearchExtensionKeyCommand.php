<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerSearchUserCommand searches for extensions by a given user
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class SearchExtensionKeyCommand extends AbstractAuthenticatedTerCommand
{

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('ter:search:extension-key')
			->setDefinition(array(
				new InputArgument('extensionKey', InputArgument::OPTIONAL, 'the extension key to search for'),
				new InputOption('width', NULL, InputOption::VALUE_OPTIONAL, 'maximum display width in columns', 80),
			))
			->setDescription('Search an extension by extension key')
            ->setHelp(<<<EOT
Search an extension by extension key

This command can be used to see what user registered an extension key even if no version has been uploaded.

Example
=======

Get information on "my_extension"

  t3xutils ter:search:extension-key my_extension

Find all extension keys that start with "my_"

  t3xutils ter:search:my_extension "my_*"
EOT
)
		;
		$this->configureSoapOptions();
		$this->configureCredentialOptions();
	}

	protected function prepareParameters(InputInterface $input, OutputInterface $output)
	{
		if(!$input->getArgument('extensionKey')) {

			$extensionKey = $this->getDialogHelper()->ask(
				$output,
				'<question>extension key:</question> '
			);
			$this->logger->debug(sprintf('interactively asked for extension key. "%s" given', $extensionKey));
			$input->setArgument('extensionKey', $extensionKey);
		}
		parent::prepareParameters($input, $output);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$command = $this->getApplication()->find('ter:search:all');

		$arguments = array(
			'command' => 'ter:search:all',
			'--username'    => $input->getOption('username'),
			'--password' => $input->getOption('password'),
			'--extensionKey' => $input->getArgument('extensionKey'),
			'--width' => $input->getOption('width'),
		);
		if($input->getOption('wsdl')) {
			$arguments['--wsdl'] = $input->getOption('wsdl');
		}

		$input = new ArrayInput($arguments);
		return $command->run($input, $output);
	}
}
