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
	public function testAction($username, $password, $extensionKey, $uploadComment, $path) {
		$upload = new \etobi\extensionUtils\Ter\TerUpload();
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