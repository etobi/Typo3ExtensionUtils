<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * search for extension keys
 */
class UploadRequest extends AbstractAuthenticatedRequest {

	public function upload(array $extensionData, array $filesData) {
		$this->createClient();
		$this->client->addArgument($extensionData);
		$this->client->addArgument($filesData);

		$response = $this->client->call('uploadExtension');

		if($response['resultCode'] !== self::TX_TER_RESULT_EXTENSIONSUCCESSFULLYUPLOADED) {
			throw new \RuntimeException(sprintf('Soap API responded with an unknown response. result code "%s"', $response['resultCode']));
		}

		return array(
			'resultMessages' => $response['resultMessages'],
			'version' => $response['version'],
		);
	}
}