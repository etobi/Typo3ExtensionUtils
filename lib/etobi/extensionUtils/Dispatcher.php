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

		$command = strtolower($command);
		if ($command != 'checkforupdate' && $command != 'selfupdate') {
			$this->checkForUpdateCommand();
		}

		$success = FALSE;
		switch (strtolower($command)) {
			case 'upload':
				$success = $this->uploadCommand($arguments);
				break;
			case 'extract':
				$success = $this->extractCommand($arguments);
				break;
			case 'checkforupdate':
				$success = $this->checkForUpdateCommand();
				break;
			case 'selfupdate':
				$success = $this->selfUpdateCommand();
				break;
			case 'version':
				$success = TRUE;
				if (@constant('T3XUTILS_VERSION')) {
					echo 'Version: ' . constant('T3XUTILS_VERSION') . chr(10);
				} else {
					echo 'Version: ?' . chr(10);
				}
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
			'version' => 'version',
			'upload' => 'upload <typo3.org-username> <typo3.org-password> <extensionKey> "<uploadComment>" <pathToExtension>',
			'extract' => 'extract <t3x-file> <destinationPath>',
			'checkforupdate' => 'checkforupdate',
			'selfupdate' => 'selfupdate'
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

	protected function checkForUpdateCommand() {
		$controller = new \etobi\extensionUtils\Controller\SelfController();
		$controller->checkForUpdateAction();
	}

	protected function selfUpdateCommand() {
		$controller = new \etobi\extensionUtils\Controller\SelfController();
		$controller->updateAction();
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

	protected function extractCommand($arguments) {
		if (count($arguments) !== 2) {
			return $this->helpCommand('extract');
		}

		$controller = new \etobi\extensionUtils\Controller\T3xController();
		$success = $controller->extractAction(
			$arguments[0],
			$arguments[1]
		);
		return $success;
	}
}