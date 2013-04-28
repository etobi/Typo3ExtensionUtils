<?php

namespace etobi\extensionUtils\Service;

/**
 * service to work with the extensions XML from typo3.org
 */
class ExtensionsXml {

    /**
     * @var string
     */
    protected $extensionsXmlFile = '/tmp/t3xutils.extensions.temp.xml';

    /**
     * @param string|null $extensionXmlFile
     */
    public function __construct($extensionXmlFile = NULL) {
        if(!is_null($extensionXmlFile)) {
            $this->extensionsXmlFile = $extensionXmlFile;
        }
    }

    /**
     * check that the
     * @throws \InvalidArgumentException
     */
    protected function ensureValidFile() {
        if(!file_exists($this->extensionsXmlFile)) {
            throw new \InvalidArgumentException(sprintf('The extension file "%s" does not exist', $this->extensionsXmlFile));
        }
        if(!is_readable($this->extensionsXmlFile)) {
            throw new \InvalidArgumentException(sprintf('The extension file "%s" is not readable', $this->extensionsXmlFile));
        }
    }

    /**
     * queryExtensionsXML
     *
     * query extension xml
     *
     * @param string $xpath xpath query
     * @throws \RuntimeException
     * @return \DOMNodeList
     */
    public function query($xpath) {
        $this->ensureValidFile();

        $doc = new \DOMDocument();
        $contents = file_get_contents($this->extensionsXmlFile);
        if($contents === FALSE) {
            throw new \RuntimeException(sprintf('Error while reading "%s"', $this->extensionsXmlFile));
        }
        if($doc->loadXML($contents) === FALSE) {
            throw new \RuntimeException(sprintf('Could not parse the contents of "%s" as XML', $this->extensionsXmlFile));
        };

        $domXpath = new \DOMXpath($doc);
        return $domXpath->query($xpath);
    }

    /**
     * find the latest version of a given extension key
     *
     * @param $extensionKey
     * @return null|string
     */
    public function findLatestVersion($extensionKey) {
        $result = $this->query(
            '/extensions/extension[@extensionkey="' . $extensionKey . '"]'
        );
        $newestDate = -1;
        $version = NULL;
        foreach ($result->item(0)->childNodes as $versionNode) {
            if ($versionNode->nodeName == 'version' && $versionNode->hasAttribute('version')) {
                $date = $versionNode->getElementsByTagName('lastuploaddate')->item(0)->nodeValue;
                if($date > $newestDate) {
                    $newestDate = $date;
                    $version = (string)$versionNode->getAttribute('version');
                }
            }
        }
        return $version;
    }
}