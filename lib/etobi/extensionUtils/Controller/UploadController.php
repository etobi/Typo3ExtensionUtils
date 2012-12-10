<?php

namespace etobi\extensionUtils\Controller;

/**
 *
 */
class UploadController {

	const TX_TER_RESULT_EXTENSIONSUCCESSFULLYUPLOADED = 10504;

	/**
	 *
	 */
	public function testAction($extensionKey, $username, $password, $uploadComment, $path) {
		$upload = new \etobi\extensionUtils\Ter\TerUpload();
		$upload->setExtensionKey($extensionKey)
			->setUsername($username)
			->setPassword($password)
			->setUploadComment($uploadComment)
			->setPath($path);

		$response = $upload->execute();

		if (!is_array($response)) {
			echo $response;
			return;
		}
		if ($response['resultCode'] == self::TX_TER_RESULT_EXTENSIONSUCCESSFULLYUPLOADED) {
			var_dump($response);
		}
	}

}