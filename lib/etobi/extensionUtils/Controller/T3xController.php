<?php

namespace etobi\extensionUtils\Controller;

/**
 *
 */
class T3xController {
	public function createAction($extensionKey, $sourcePath, $t3xFilePath) {
		$sourcePath = rtrim($sourcePath, '/') . '/';

		if (!is_dir($sourcePath)) {
			throw new \Exception('Cant read "' . $sourcePath . '"');
		}
		if (!file_exists($sourcePath . 'ext_emconf.php')) {
			throw new \Exception('ext_emconf.php missing in "' . $sourcePath . '"');
		}
		if (file_exists($t3xFilePath)) {
			throw new \Exception('File already exists "' . $t3xFilePath . '"');
		}
		if (!is_writable(dirname($t3xFilePath))) {
			throw new \Exception('Cant write "' . $t3xFilePath . '"');
		}

		$extensionData = array(
			'extKey' => $extensionKey,
			'EM_CONF' => \etobi\extensionUtils\ter\Helper::getEmConf($extensionKey, $sourcePath),
			'misc' => array(),
			'techInfo' => array(),
			'FILES' => \etobi\extensionUtils\ter\Helper::getExtensionFilesData($sourcePath)
		);
		$data = serialize($extensionData);
		$md5 = md5($data);
		$compress = '';
		if (function_exists('gzcompress')) {
			$compress = 'gzcompress';
			$data = gzcompress($data);
		}
		$success = file_put_contents(
			$t3xFilePath,
				$md5 . ':' . $compress . ':' . $data
		);
		if ($success === FALSE) {
			throw new \Exception('Error writing "' . $t3xFilePath . '"');
		}
		echo 'created "' . $t3xFilePath . '"' . chr(10);
	}

	/**
	 * @param string $t3xFilePath
	 * @param string $destinationPath
	 * @throws \Exception
	 */
	public function extractAction($t3xFilePath, $destinationPath) {
		$destinationPath = rtrim($destinationPath, '/') . '/';

		if (!is_dir($destinationPath)) {
			mkdir($destinationPath, 0777, TRUE);
		}
		if (!is_dir($destinationPath) || !is_writeable($destinationPath)) {
			throw new \Exception('Cant write to "' . $destinationPath . '"');
		}
		if (file_exists($destinationPath . 'ext_emconf.php')) {
			throw new \Exception('Destination directory is not empty "' . $destinationPath . '"');
		}
		if (!file_exists($t3xFilePath) || !is_file($t3xFilePath) || !is_readable($t3xFilePath)) {
			throw new \Exception('Cant read "' . $t3xFilePath . '"');
		}

		$extensionData = $this->extractExtensionDataFromT3x($t3xFilePath);
		$this->writeFiles($extensionData['FILES'], $destinationPath);
		$this->writeEmConf($extensionData['extKey'], $extensionData['EM_CONF'], $destinationPath);
		echo 'extracted "' . $t3xFilePath . '" to "' . $destinationPath . '"' . chr(10);
	}

	/**
	 * @param string $extKey
	 * @param array $emConf
	 * @param string $destinationPath
	 */
	protected function writeEmConf($extKey, $emConf, $destinationPath) {
		$code = '<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "' . $extKey . '".
 *
 * Auto generated ' . date('d-m-Y H:i') . '
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = ' . var_export($emConf, TRUE) . ';

?>';
		$code = str_replace('  ', "\t", $code);

		$fileHandler = fopen($destinationPath . 'ext_emconf.php', 'wb');
		fwrite($fileHandler, $code);
		fclose($fileHandler);
	}

	/**
	 * @param array $files
	 * @param string $destinationPath
	 * @throws \Exception
	 */
	protected function writeFiles($files, $destinationPath) {
		if (!is_array($files)) return;
		foreach ($files as $info) {
			$fullFilePath = $destinationPath . $info['name'];
			if (!is_dir(dirname($fullFilePath))) {
				mkdir(dirname($fullFilePath), 0777, TRUE);
			}
			if ($info['content_md5'] !== md5($info['content'])) {
				throw new \Exception('MD5 hash of "' . $info['name'] . '" doesnt match');
			} else {
				$fileHandler = fopen($fullFilePath, 'wb');
				if (empty($info['content'])) {
					$res = TRUE;
				} else {
					$res = fwrite($fileHandler, $info['content']);
				}
				fclose($fileHandler);
				if (!$res) {
					throw new \Exception('Cant write file "' . $fullFilePath . '"');
				}
			}
		}
	}

	/**
	 * @param string $t3xFilePath
	 * @return array
	 * @throws \Exception
	 */
	protected function extractExtensionDataFromT3x($t3xFilePath) {
		$content = file_get_contents($t3xFilePath);
		$parts = explode(':', $content, 3);
		if ($parts[1] === 'gzcompress') {
			if (function_exists('gzuncompress')) {
				$parts[2] = gzuncompress($parts[2]);
			} else {
				throw new \Exception('Decoding Error: No decompressor available for compressed content. gzcompress()/gzuncompress() functions are not available!');
			}
		}
		if (md5($parts[2]) == $parts[0]) {
			$output = unserialize($parts[2]);
			if (is_array($output)) {
				return $output;
			} else {
				throw new \Exception('Error: Content could not be unserialized to an array. Strange (since MD5 hashes match!)');
			}
		} else {
			throw new \Exception('Error: MD5 mismatch. Maybe the extension file was downloaded and saved as a text file by the browser and thereby corrupted!? (Always select "All" filetype when saving extensions)');
		}
	}
}