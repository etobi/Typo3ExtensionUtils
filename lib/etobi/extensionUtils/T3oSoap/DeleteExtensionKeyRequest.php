<?php

namespace etobi\extensionUtils\T3oSoap;
use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyHasUploadsException;
use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotExistsException;

/**
 * delete an extension key without any uploads
 */
class DeleteExtensionKeyRequest extends AbstractAuthenticatedRequest {

	/**
	 * delete a given extension key
	 *
	 * @param $extensionKey
	 * @throws Exception\ExtensionKeyHasUploadsException
	 * @throws \RuntimeException
	 * @throws Exception\ExtensionKeyNotExistsException
	 * @return bool
	 */
    public function deleteExtensionKey($extensionKey)
    {
        $extensionKey = (string)$extensionKey;

        $this->createClient();
        $this->client->addArgument($extensionKey);

        $result = $this->client->call('deleteExtensionKey');

        if($result['resultCode'] == self::TX_TER_RESULT_GENERAL_OK){
            return TRUE;
        } elseif($result['resultCode'] == self::TX_TER_ERROR_DELETEEXTENSIONKEY_CANTDELETEBECAUSEVERSIONSEXIST) {
	        throw new ExtensionKeyHasUploadsException(sprintf(
		        'The extension key "%s" has uploaded versions and can not be deleted',
		        $extensionKey
	        ));
        } elseif($result['resultCode'] == self::TX_TER_ERROR_DELETEEXTENSIONKEY_KEYDOESNOTEXIST) {
            throw new ExtensionKeyNotExistsException(sprintf(
                'the extension key "%s" is not registered and can not be deleted',
                $extensionKey
            ));
        } else {
            throw new \RuntimeException(sprintf('Soap API responded with an unknown response. result code "%s"', $result['resultCode']));
        }
    }
}