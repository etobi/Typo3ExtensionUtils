<?php

namespace etobi\extensionUtils\Service;

/**
 * a wrapper to download a file to a specific
 */
class Downloader {

    /**
     * GETs the content of an urls and stores it as a file
     *
     * @param string         $url               the url to GET
     * @param string         $path              the path to write the content to
     * @param callback|null  $progressCallback  an optional callback that tracks progress (gets two parameters: downloaded size, total size)
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function downloadFile($url, $path, $progressCallback = null) {
        if(!function_exists('curl_init')) {
            throw new \RuntimeException('curl has to be enabled as PHP module');
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $fh = fopen($path, 'w');
        if(!$fh) {
            throw new \RuntimeException(sprintf('The file "%s" could not be opened to write to.', $path));
        }
        curl_setopt($ch, CURLOPT_FILE, $fh);

        if($progressCallback) {
            if(!defined('CURLOPT_PROGRESSFUNCTION')) {
                throw new \RuntimeException('The progress callback is only supported from PHP 5.3 up');
            }
            if(!is_callable($progressCallback)) {
                throw new \InvalidArgumentException('no valid callback given in ' . __CLASS__ . '::' . __METHOD__);
            }
            curl_setopt($ch, CURLOPT_NOPROGRESS, false);
            curl_setopt($ch, CURLOPT_PROGRESSFUNCTION, $progressCallback);
        }

        curl_exec($ch);
        fclose($fh);
    }
}