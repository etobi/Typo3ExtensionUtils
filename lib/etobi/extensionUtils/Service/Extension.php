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

    /**
     * get the extension name and version as an array from a filename of the form "fooext_1.2.3.t3x"
     *
     * @param string $basename
     * @return array            first value is the name, second the version
     */
    public function getExtensionNameAndVersionFromFileName($basename) {
        $basename = $this->removeFileNameExtension($basename);
        $matches = array();
        if(preg_match('/^([a-z_]+)_(\d+.\d+.\d+)$/ims', $basename, $matches) > 0) {
            return array($matches[1], $matches[2]);
        } else {
            return array($basename, NULL);
        }
    }

    /**
     * get the extension name from a filename of the form "fooext_1.2.3.t3x"
     *
     * @param $basename
     * @return string
     */
    public function getExtensionNameFromFileName($basename) {
        list($name, $version) = $this->getExtensionNameAndVersionFromFileName($basename);
        return $name;
    }

    /**
     * get the extension version from a filename of the form "fooext_1.2.3.t3x"
     *
     * @param $basename
     * @return string|null
     */
    public function getExtensionVersionFromFileName($basename) {
        list($name, $version) = $this->getExtensionNameAndVersionFromFileName($basename);
        return $version;
    }

    /**
     * remove trailing file name extensions
     *
     * @param $basename
     * @return string
     */
    protected function removeFileNameExtension($basename) {
        $rpos = strrpos($basename, '.');
        if($rpos !== FALSE) {
            $basename = substr($basename, 0, $rpos);
        }
        return $basename;
    }

}