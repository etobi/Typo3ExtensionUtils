<?php

namespace etobi\extensionUtils\Controller;

/**
 *
 */
class SelfController extends AbstractController {

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
            if($this->logger) {
                $this->logger->notice('You\'re using a different version then the latest available on github. Consider updating using the "selfupdate" command.');
                $this->logger->notice('Visit ' . $this->homepage . ' for more information.');
            }
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
                    if($this->logger) {
					    $this->logger->info('download ' . $this->pharDownloadUrl . ' ...');
                    }
					$downloadCommand = 'wget -q ' . $this->pharDownloadUrl . ' -O ' . $tempFilename;
					exec($downloadCommand);
					chmod($tempFilename, 0777 & ~umask());

                    if($this->logger) {
					    $this->logger->info('check download ' . ' ...');
                    }
					$testPhar = new \Phar($tempFilename);
					unset($testPhar);

                    if($this->logger) {
                        $this->logger->info('install to ' . $localFilename . ' ...');
                    }
					@unlink($backupFilename);
					rename($localFilename, $backupFilename);
					rename($tempFilename, $localFilename);

                    if($this->logger) {
					    $this->logger->notice('done ("' . $sha1 . '")');
                    }
				} catch (\Exception $e) {
					@unlink($tempFilename);
					if (!$e instanceof \UnexpectedValueException && !$e instanceof \PharException) {
						throw $e;
					}
					throw new \Exception('The download is corrupted ('.$e->getMessage().').');
				}
			} else {
                if($this->logger) {
				    $this->logger->notice('already up-to-date.');
                }
			}
		} else {
			throw new \Exception('selfupdate does only work, when running the .phar');
		}
		return;
	}

	/**
	 * @return bool|string
	 */
	protected function getPharVersion() {
		return @file_get_contents($this->pharVersionUrl);
	}
}