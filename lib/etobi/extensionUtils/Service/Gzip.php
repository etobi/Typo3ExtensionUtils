<?php

namespace etobi\extensionUtils\Service;

/**
 * a wrapper to gzip and ungzip a file
 */
class Gzip {

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
        $cmd = $this->createCommand($source, $destination, '-df');

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
    protected function createCommand($source, $destination, $flags = '') {
        return sprintf(
            '%s %s %s > %s',
            $this->bin,
            $flags,
            escapeshellarg($source),
            escapeshellarg($destination)
        );
    }
}