<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerSearchAllCommand searches for extensions in TER
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class SearchAllCommand extends AbstractAuthenticatedTerCommand
{

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$this
			->setName('ter:search:all')
			->setDefinition(array(
				new InputOption('user', NULL, InputOption::VALUE_REQUIRED, 'search by user name'),
				new InputOption('extensionKey', NULL, InputOption::VALUE_REQUIRED, 'the extension key to search'),
				new InputOption('title', NULL, InputOption::VALUE_REQUIRED, 'search by extension title'),
				new InputOption('description', NULL, InputOption::VALUE_REQUIRED, 'search by description'),
				new InputOption('width', NULL, InputOption::VALUE_OPTIONAL, 'maximum display width in columns', 80),
			))
			->setDescription('Search an extension key that matches all the given limitations')
            ->setHelp(<<<EOT
Search an extension key.

If you set multiple options, they are connected with AND.

Example
=======

Show all extensions that have "news" in their title

  t3xutils ter:search:all --title="*news*"

Show all extensions by the user "john.doe" that have "news" in their description

  t3xutils ter:search:all --user="john.doe" --title="*news*"

.t3xuconfig
===========

* <info>ter.username</info>: username on typo3.org
* <info>ter.password</info>: password on typo3.org
* <info>ter.wsdl</info>: wsdl url for the Soap API

Example
=======

Search all extensions that have a description mentioning "tt_news" and are created by "rupi"

  t3xutils ter:search:all --description="*tt_news*" --user="rupi"

EOT
)
		;
		$this->configureSoapOptions();
		$this->configureCredentialOptions();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		/**
		 * @var $search \etobi\extensionUtils\T3oSoap\SearchRequest
		 */
		$search = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\SearchRequest');
		$results = $search->search(
			str_replace('*', '%', $input->getOption('user')),
			str_replace('*', '%', $input->getOption('extensionKey')),
			str_replace('*', '%', $input->getOption('title')),
			str_replace('*', '%', $input->getOption('description'))
		);
		if(count($results) == 0) {
			$this->output->writeln('Nothing found');
		} else {
			foreach($results as $result) {
				$this->printExtensionInfo($result);
			}
		}
	}

	/**
	 * print extension information into the output
	 *
	 * @param array $data
	 * @return void
	 */
	protected function printExtensionInfo(array $data) {
		$this->output->writeln('<comment>' . $data['title'] . '</comment>');
		$maxKeyStrlen = 1;

		foreach($data as $key=>$value) {
			$strlen = strlen($key);
			if($strlen > $maxKeyStrlen) {
				$maxKeyStrlen = $strlen;
			}
		}

		$maxValueStrlen = max($this->input->getOption('width') - $maxKeyStrlen - 2, 1);

		$this->logger->debug('width of key column is ' . $maxKeyStrlen);
		$this->logger->debug('width of value column is ' . $maxValueStrlen);


		$lineFormat = '%-' . $maxKeyStrlen . 's  %-' . $maxValueStrlen . 's';

		// print data
		foreach($data as $key=>$value) {
			$value = wordwrap(trim($value), $maxValueStrlen);
			if(!is_array($value)) {
				$value = explode("\n", $value);
			}

			// print first line
			$this->output->writeln(sprintf(
				$lineFormat,
				$key,
				array_shift($value)
			));
			while($row = array_shift($value)) {
				$this->output->writeln(sprintf(
					$lineFormat,
					'',
					$row
				));
			}
		}
	}
}
