<?php

namespace etobi\extensionUtils\Service;

/**
 * service to work with T3X files
 */
class T3xFile {
    /**
     * create a T3X file from a directory
     *
     * overrides existing files
     *
     * @param string $extensionKey
     * @param string $sourcePath
     * @param string $t3xFilePath
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function create($extensionKey, $sourcePath, $t3xFilePath) {
        $sourcePath = rtrim($sourcePath, '/') . '/';

        if (!is_dir($sourcePath)) {
            throw new \InvalidArgumentException(sprintf('Can\'t read "%s"', $sourcePath));
        }
        if (!file_exists($sourcePath . 'ext_emconf.php')) {
            throw new \InvalidArgumentException(sprintf('ext_emconf.php missing in "%s"', $sourcePath));
        }
        if (!is_writable(dirname($t3xFilePath))) {
            throw new \InvalidArgumentException(sprintf('Can\'t write "%s"', $t3xFilePath));
        }

	    $emConf = new EmConf($sourcePath);
        $extensionData = array(
            'extKey' => $extensionKey,
            'EM_CONF' => $emConf->toArray(),
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
            throw new \RuntimeException(sprintf('Error writing "%s"', $t3xFilePath));
        }
        return file_exists($t3xFilePath);
    }

    /**
     * @param string $t3xFilePath
     * @param string $destinationPath
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function extract($t3xFilePath, $destinationPath) {
        $destinationPath = rtrim($destinationPath, '/') . '/';

        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, TRUE);
        }
        if (!is_dir($destinationPath) || !is_writeable($destinationPath)) {
            throw new \InvalidArgumentException(sprintf('Can\'t write to "%s"', $destinationPath));
        }
        if (file_exists($destinationPath . 'ext_emconf.php')) {
            throw new \InvalidArgumentException(sprintf('Destination directory is not empty "%s".', $destinationPath));
        }
        if (!file_exists($t3xFilePath) || !is_file($t3xFilePath) || !is_readable($t3xFilePath)) {
            throw new \InvalidArgumentException(sprintf('Can\'t read "%s"', $t3xFilePath));
        }

        $extensionData = $this->extractExtensionDataFromT3x($t3xFilePath);
        $this->writeFiles($extensionData['FILES'], $destinationPath);
        $this->writeEmConf($extensionData['extKey'], $extensionData['EM_CONF'], $destinationPath);
        return file_exists($destinationPath);
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
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    protected function writeFiles($files, $destinationPath) {
        if (!is_array($files)) return;
        foreach ($files as $info) {
            $fullFilePath = $destinationPath . $info['name'];
            if (!is_dir(dirname($fullFilePath))) {
                mkdir(dirname($fullFilePath), 0777, TRUE);
            }
            if ($info['content_md5'] !== md5($info['content'])) {
                throw new \RuntimeException(sprintf('MD5 hash of "%s" doesnt match', $info['name']));
            } else {
                $fileHandler = fopen($fullFilePath, 'wb');
                if (empty($info['content'])) {
                    $res = TRUE;
                } else {
                    $res = fwrite($fileHandler, $info['content']);
                }
                fclose($fileHandler);
                if (!$res) {
                    throw new \InvalidArgumentException(sprintf('Can\'t write file "%s"', $fullFilePath));
                }
            }
        }
    }

    /**
     * @param string $t3xFilePath
     * @throws \RuntimeException
     * @return array
     */
    protected function extractExtensionDataFromT3x($t3xFilePath) {
        $content = file_get_contents($t3xFilePath);
        $parts = explode(':', $content, 3);
        if ($parts[1] === 'gzcompress') {
            if (function_exists('gzuncompress')) {
                $parts[2] = gzuncompress($parts[2]);
            } else {
                throw new \RuntimeException('Decoding Error: No decompressor available for compressed content. gzcompress()/gzuncompress() functions are not available!');
            }
        }
        if (md5($parts[2]) == $parts[0]) {
            $output = unserialize($parts[2]);
            if (is_array($output)) {
                return $output;
            } else {
                throw new \RuntimeException('Error: Content could not be unserialized to an array. Strange (since MD5 hashes match!)');
            }
        } else {
            throw new \RuntimeException('Error: MD5 mismatch. Maybe the extension file was downloaded and saved as a text file by the browser and thereby corrupted!? (Always select "All" filetype when saving extensions)');
        }
    }
}