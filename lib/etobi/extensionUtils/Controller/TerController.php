<?php

namespace etobi\extensionUtils\Controller;

/**
 *
 */
class TerController {

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
	public function fetchAction($extensionKey, $version, $destinationPath = NULL) {
		$t3xFile = $extensionKey . '_' . $version . '.t3x';
		$url = 'http://typo3.org/fileadmin/ter/' . $extensionKey{0} . '/' . $extensionKey{1} . '/' . $t3xFile;
		exec('wget "' . $url . '" -O ' . ($destinationPath ?: '.') . '/' . $t3xFile);
	}

	/**
	 *
	 */
	public function updateAction() {
		 // TODO
		echo 'fetch extension info ...' . chr(10);
		$url = 'http://typo3.org/fileadmin/ter/extensions.xml.gz';
		exec('wget "' . $url . '" -q -O - | gunzip > ' . $this->extensionsXmlFile);
	}

	/**
	 *
	 */
	public function infoAction($extensionKey, $version = NULL) {
		echo (time() - filemtime($this->extensionsXmlFile));
		if (!file_exists($this->extensionsXmlFile) || (time() - filemtime($this->extensionsXmlFile)) > 3600) {
			$this->updateAction();
		}

		$doc = new \DOMDocument();
		$doc->loadXML(file_get_contents($this->extensionsXmlFile));
		$xpath = new \DOMXpath($doc);
		$result = $xpath->query(
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

			echo 'Extension: ' . $extensionKey . ' ' . $version;
			foreach ($infos as $key => $info) {
				if ($key === 'lastuploaddate') {
					$info = date('d.m.Y H:i:s', $info);
				} else if ($key === 'dependencies') {
					$info = var_export(unserialize($info), TRUE);
				}
				echo ' ' .
						str_pad($key, 15, ' ', STR_PAD_RIGHT) .
						'    ' .
						$info .
						chr(10);
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

			echo 'Available versions:' . chr(10);
			foreach($versionInfos as $versionInfo) {
				echo ' ' .
						$versionInfo['version'] .
						'    uploaded: ' .
						date('d.m.Y H:i:s', $versionInfo['timestamp']) .
						// chr(10) .
						// $versionInfo['comment'] .
						chr(10);
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
			echo 'SOAP-Error: ' . $s->getMessage() . chr(10);
			return FALSE;
		} catch(\Exception $e) {
			echo 'Error: ' . $e->getMessage() . chr(10);
			return FALSE;
		}

		if (!is_array($response)) {
			echo 'Error: ' . $response . chr(10);
			return FALSE;
		}
		if ($response['resultCode'] == self::TX_TER_RESULT_EXTENSIONSUCCESSFULLYUPLOADED) {
			var_dump($response);
			return TRUE;
		}
		return FALSE;
	}
}