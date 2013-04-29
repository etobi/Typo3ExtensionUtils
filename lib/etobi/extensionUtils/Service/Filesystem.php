<?php

namespace etobi\extensionUtils\Service;

/**
 * a wrapper to gzip and ungzip a file
 */
class Filesystem {

    protected $bin = 'gzip';

    /**
     * unzip a file
     *
     * The source file will be deleted
     *
     * @param $source
     * @param $destination
     * @return bool
     * @throws \RuntimeException
     */
    public function unzip($source, $destination) {
        $cmd = $this->createGzipCommand($source, $destination, '-df');

        $returnCode = 0;
        system($cmd, $returnCode);
        if($returnCode !== 0) {
            throw new \RuntimeException(sprintf('The command "%s" exit with code %d.', $cmd, $returnCode));
        }
        return file_exists($destination);
    }

    /**
     * @param string $source
     * @param string $destination
     * @param string $flags
     * @return string
     */
    protected function createGzipCommand($source, $destination, $flags = '') {
        return sprintf(
            '%s %s %s > %s',
            $this->bin,
            $flags,
            escapeshellarg($source),
            escapeshellarg($destination)
        );
    }

    /**
     * @author <erkethan at free dot fr>
     * @see http://php.net/manual/de/function.rmdir.php#92050
     * @param $dir
     * @return bool
     */
    protected function deleteDirectory($dir) {
        if (!file_exists($dir)) return true;
        if (!is_dir($dir) || is_link($dir)) return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') continue;
            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                chmod($dir . DIRECTORY_SEPARATOR . $item, 0777);
                if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) return false;
            };
        }
        return rmdir($dir);
    }
}