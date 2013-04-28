<?php

namespace etobi\extensionUtils\Service;

/**
 *
 */
class Extension {

    protected $extensionDownloadPath = 'http://typo3.org/fileadmin/ter';

    /**
     * get the uri to download an extension from
     *
     * no plausibility check performed, so the file might not exist
     *
     * @param $extensionName
     * @param $version
     * @throws \InvalidArgumentException
     * @return string
     */
    public function getDownloadUri($extensionName, $version) {
        if(strlen($extensionName) < 3) {
            throw new \InvalidArgumentException('The extension name must at least be 3 chars long');
        }
        return sprintf(
            '%s/%s/%s/%s_%s.t3x',
            $this->extensionDownloadPath,
            $extensionName{0},
            $extensionName{1},
            $extensionName,
            $version
        );
    }

}