<?php

namespace etobi\extensionUtils\Controller;

/**
 *
 */
class SelfController {

	/**
	 * @var string
	 */
	protected $repository = 'https://github.com/etobi/Typo3ExtensionUtils.git';

	/**
	 * @var string
	 */
	protected $homepage = 'https://github.com/etobi/Typo3ExtensionUtils';

	/**
	 * @var string
	 */
	protected $refs = 'refs/heads/master';

	/**
	 * @var string
	 */
	protected $pharDownloadUrl = 'https://github.com/etobi/Typo3ExtensionUtils/raw/master/bin/t3xutils.phar';

	/**
	 * @var string
	 */
	protected $pharVersionUrl = 'https://github.com/etobi/Typo3ExtensionUtils/raw/master/pharVersion.txt';

	/**
	 *
	 */
	public function checkForUpdateAction() {
		$sha1 = $this->getPharVersion();
		if ($sha1 && $sha1 !== @constant('T3XUTILS_VERSION')) {
			echo 'You\'re using a different version then the latest available on github. Consider updating using the "selfupdate" command.' . chr(10);
			echo 'Visit ' . $this->homepage . ' for more information.' . chr(10);
			echo chr(10);
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/**
	 * @throws \Exception
	 */
	public function updateAction() {
		$localFilename = $_SERVER['argv'][0];
		$backupFilename = basename($localFilename, '.phar').'-backup.phar';
		$tempFilename = basename($localFilename, '.phar').'-temp.phar';

		if (@constant('T3XUTILS_VERSION')) {
			$sha1 = $this->getPharVersion();
			if ($sha1 && $sha1 !== @constant('T3XUTILS_VERSION')) {
				try {
					echo 'download ' . $this->pharDownloadUrl . ' ...' . chr(10);
					$downloadCommand = 'wget -q ' . $this->pharDownloadUrl . ' -O ' . $tempFilename;
					exec($downloadCommand);
					chmod($tempFilename, 0777 & ~umask());

					echo 'check download ' . ' ...' . chr(10);
					$testPhar = new \Phar($tempFilename);
					unset($testPhar);

					echo 'install to ' . $localFilename . ' ...' . chr(10);
					@unlink($backupFilename);
					rename($localFilename, $backupFilename);
					rename($tempFilename, $localFilename);

					echo 'done ("' . $sha1 . '")' . chr(10);
				} catch (\Exception $e) {
					@unlink($tempFilename);
					if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
						throw $e;
					}
					throw new \Exception('The download is corrupted ('.$e->getMessage().').');
				}
			} else {
				echo 'already up-to-date.' . chr(10);
			}
		} else {
			throw new \Exception('selfupdate does only work, when running the .phar');
		}
		return true;
	}

	/**
	 * @return bool|string
	 */
	protected function getPharVersion() {
		return @file_get_contents($this->pharVersionUrl);
	}
}