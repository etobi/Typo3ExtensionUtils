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
				new InputOption('user', NULL, InputArgument::OPTIONAL, 'search by user name'),
				new InputOption('extensionKey', NULL, InputArgument::OPTIONAL, 'the extension key to search'),
				new InputOption('title', NULL, InputArgument::OPTIONAL, 'search by extension title'),
				new InputOption('description', NULL, InputArgument::OPTIONAL, 'search by description'),
				new InputOption('width', NULL, InputOption::VALUE_OPTIONAL, 'maximum display width in columns', 80),
			))
			->setDescription('Search an extension that matches all the given limitations')
			//@TODO: longer help text
//            ->setHelp()
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
			$input->getOption('user'),
			$input->getOption('extensionKey'),
			$input->getOption('title'),
			$input->getOption('description')
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
