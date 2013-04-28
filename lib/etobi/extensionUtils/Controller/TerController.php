<?php

namespace etobi\extensionUtils\Controller;

use etobi\extensionUtils\Service\FileSizeProgressBar;

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
	 * @param string $extensionKey
	 * @param string $version
	 */
	public function infoAction($extensionKey, $version = NULL) {

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