<?php

namespace etobi\extensionUtils;

class Dispatcher {

	/**
	 * @var array
	 */
	protected $arguments;

	/**
	 * @var string
	 */
	protected $commandCalled;

	/**
	 * @param array $arguments
	 */
	public function setArguments($arguments) {
		$this->arguments = $arguments;
	}

	/**
	 * @param string $commandCalled
	 */
	public function setCommandCalled($commandCalled) {
		$this->commandCalled = $commandCalled;
	}

	/**
	 *
	 */
	public function run() {
		$command = isset($this->arguments[0]) ? $this->arguments[0] : 'help';
		$arguments = array_splice($this->arguments, 1);

		if (empty($command)) {
			$command = 'help';
		}

		$success = FALSE;
		switch ($command) {
			case 'upload':
				$success = $this->uploadCommand($arguments);
				break;
			default:
			case 'help':
				$success = $this->helpCommand(isset($arguments[0]) ? $arguments[0] : NULL);
				break;
		}

		exit($success ? 0 : 2);
	}

	/**
	 *
	 */
	protected function helpCommand($command = NULL) {
		$usages = array(
			'help' => 'help',
			'upload' => 'upload <typo3.org-username> <typo3.org-password> <extensionKey> "<uploadComment>" <pathToExtension>',
		);
		echo 'Usage: ';
		if ($command) {
			echo $this->commandCalled . ' ' . $usages[$command] . chr(10);
		} else {
			$isFirst = TRUE;
			foreach ($usages as $usage) {
				if (!$isFirst) {
					echo '       ';
				}
				$isFirst = FALSE;
				echo $this->commandCalled . ' ' . $usage . chr(10);
			}
		}
		return TRUE;
	}

	/**
	 * @param array $arguments
	 * @return bool
	 */
	protected function uploadCommand($arguments) {
		if (count($arguments) !== 5) {
			return $this->helpCommand('upload');
		}

		$controller = new \etobi\extensionUtils\Controller\UploadController();
		$success = $controller->uploadAction(
			$arguments[0],
			$arguments[1],
			$arguments[2],
			$arguments[3],
			$arguments[4]
		);
		return $success;
	}
}