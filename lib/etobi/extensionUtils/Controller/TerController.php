<?php

namespace etobi\extensionUtils\Controller;

/**
 *
 */
class TerController extends AbstractController {

	const TX_TER_RESULT_EXTENSIONSUCCESSFULLYUPLOADED = 10504;

	/**
	 * @var string
	 */
	protected $extensionsXmlFile = '/tmp/t3xutils.extensions.temp.xml';

	/**
	 * @param $extensionKey
	 * @param $version
	 * @param string $destinationPath
	 */
	public function fetchAction($extensionKey, $version = NULL, $destinationPath = NULL) {
		// Find latest version
		if ($version === NULL) {
			$result = $this->queryExtensionsXML(
				'/extensions/extension[@extensionkey="' . $extensionKey . '"]'
			);
			$max = -1;
			foreach ($result->item(0)->childNodes as $versionNode) {
				if ($versionNode->nodeName == 'version' && $versionNode->hasAttribute('version')) {
					$date = $versionNode->getElementsByTagName('lastuploaddate')->item(0)->nodeValue;
					if($date > $max) {
						$max = $date;
						$version = $versionNode->getAttribute('version');
					}
				}
			}
            if($this->logger) {
                if (!$version) {
                    $this->logger->warning('could not find latest version of ' . $extensionKey);
                } else {
                    $this->logger->notice('fetching latest version of ' . $extensionKey . ', ' . $version);
                }
            }
		}
		$t3xFile = $extensionKey . '_' . $version . '.t3x';
		$url = 'http://typo3.org/fileadmin/ter/' . $extensionKey{0} . '/' . $extensionKey{1} . '/' . $t3xFile;
		exec('wget "' . $url . '" -O ' . ($destinationPath ?: '.') . '/' . $t3xFile);
	}

	/**
	 *
	 */
	public function updateAction() {
		 // TODO
        if($this->logger) {
            $this->logger->info('fetch extension info ...');
        }
		$url = 'http://typo3.org/fileadmin/ter/extensions.xml.gz';
		exec('wget "' . $url . '" -q -O - | gunzip > ' . $this->extensionsXmlFile);
	}

	/**
	 * @param string $extensionKey
	 * @param string $version
	 */
	public function infoAction($extensionKey, $version = NULL) {
		$result = $this->queryExtensionsXML(
			'/extensions/extension[@extensionkey="' . $extensionKey . '"]' .
				($version ? '/version[@version="' . $version . '"]' : '')
		);
		if ($version) {
			$infos = array();
			foreach ($result->item(0)->childNodes as $childNode) {
				/** @var $childNode \DOMElement */
				if ($childNode->nodeType == XML_ELEMENT_NODE) {
					$infos[$childNode->nodeName] = $childNode->nodeValue;
				}
			}

            if($this->logger) {
                $this->logger->notice('Extension: ' . $extensionKey . ' ' . $version);
            }
			foreach ($infos as $key => $info) {
				if ($key === 'lastuploaddate') {
					$info = date('d.m.Y H:i:s', $info);
				} else if ($key === 'dependencies') {
					$info = var_export(unserialize($info), TRUE);
				}
                if($this->logger) {
                    $this->logger->notice(' ' .
						str_pad($key, 15, ' ', STR_PAD_RIGHT) .
						'    ' .
						$info
                    );
                }
			}

		} else {
			$versionInfos = array();
			foreach ($result->item(0)->childNodes as $versionNode) {
				/** @var $versionNode \DOMElement */
				if ($versionNode->nodeName == 'version' && $versionNode->hasAttribute('version')) {
					$versionInfos[] = array(
						'version' => $versionNode->getAttribute('version'),
						'comment' => $versionNode->getElementsByTagName('uploadcomment')->item(0)->nodeValue,
						'timestamp' => $versionNode->getElementsByTagName('lastuploaddate')->item(0)->nodeValue
					);
				}
			}

            if($this->logger) {
                $this->logger->notice('Available versions:');
                foreach($versionInfos as $versionInfo) {
                    $this->logger->notice(' ' .
                        $versionInfo['version'] .
                        '    uploaded: ' .
                        date('d.m.Y H:i:s', $versionInfo['timestamp'])
                        // chr(10) .
                        // $versionInfo['comment']
                    );
                }
            }


		}
	}

	/**
	 * @param $username
	 * @param $password
	 * @param $extensionKey
	 * @param $uploadComment
	 * @param $path
	 * @return bool
	 */
	public function uploadAction($username, $password, $extensionKey, $uploadComment, $path) {
		$upload = new \etobi\extensionUtils\ter\TerUpload();
		$upload->setExtensionKey($extensionKey)
			->setUsername($username)
			->setPassword($password)
			->setUploadComment($uploadComment)
			->setPath($path);

		try {
			$response = $upload->execute();
		} catch (\SoapFault $s) {
            if($this->logger) {
                $this->logger->error('SOAP-Error: ' . $s->getMessage());
            }
			return FALSE;
		} catch(\Exception $e) {
            if($this->logger) {
                $this->logger->error('Error: ' . $e->getMessage());
            }
			return FALSE;
		}

		if (!is_array($response)) {
            if($this->logger) {
			    $this->logger->error('Error: ' . $response);
            }
			return FALSE;
		}
		if ($response['resultCode'] == self::TX_TER_RESULT_EXTENSIONSUCCESSFULLYUPLOADED) {
			var_dump($response);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * queryExtensionsXML
	 *
	 * query extension xml
	 *
	 * @param $query xpath query
	 * @returns DOMNodeList
	 */
	protected function queryExtensionsXML($query) {
		if (!file_exists($this->extensionsXmlFile) || (time() - filemtime($this->extensionsXmlFile)) > 3600) {
			$this->updateAction();
		}

		$doc = new \DOMDocument();
		$doc->loadXML(file_get_contents($this->extensionsXmlFile));
		$xpath = new \DOMXpath($doc);
		return $xpath->query($query);
	}
}