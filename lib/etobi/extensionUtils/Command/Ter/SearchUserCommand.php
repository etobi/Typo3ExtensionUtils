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
class SearchUserCommand extends AbstractAuthenticatedTerCommand
{

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('ter:search:user')
			->setDefinition(array(
				new InputArgument('user', InputArgument::OPTIONAL, 'search by user name'),
				new InputOption('width', NULL, InputOption::VALUE_OPTIONAL, 'maximum display width in columns', 80),
			))
			->setDescription('Search an extension by username')
            ->setHelp(<<<EOT
Search an extension key by user

Example
=======

List all extension keys by user "kasper"

  t3xutils ter:search:user kasper
EOT
)
		;
		$this->configureSoapOptions();
		$this->configureCredentialOptions();
	}

	protected function prepareParameters(InputInterface $input, OutputInterface $output)
	{
		if(!$input->getArgument('user')) {

			$user = $this->getDialogHelper()->ask(
				$output,
				'<question>search for user:</question> '
			);
			$this->logger->debug(sprintf('interactively asked for user. "%s" given', $user));
			$input->setArgument('user', $user);
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
			'--user' => $input->getArgument('user'),
			'--width' => $input->getOption('width'),
		);
		if($input->getOption('wsdl')) {
			$arguments['--wsdl'] = $input->getOption('wsdl');
		}

		$input = new ArrayInput($arguments);
		return $command->run($input, $output);
	}
}
