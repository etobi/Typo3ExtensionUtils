<?php

namespace etobi\extensionUtils\T3oSoap;
use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException;

/**
 * check if a given extension key is available
 */
class CheckExtensionKeyRequest extends AbstractAuthenticatedRequest {

    /**
     * check if a given extension key is available
     *
     * @param $extensionKey
     * @throws \RuntimeException
     * @throws Exception\ExtensionKeyNotValidException
     * @return bool
     */
    public function checkExtensionKey($extensionKey)
    {
        $extensionKey = (string)$extensionKey;

        $this->createClient();
        $result = $this->client->call('checkExtensionKey', $extensionKey);

        if($result['resultCode'] == self::TX_TER_RESULT_EXTENSIONKEYDOESNOTEXIST){
            return TRUE;
        } elseif($result['resultCode'] == self::TX_TER_RESULT_EXTENSIONKEYALREADYEXISTS) {
            return FALSE;
        } elseif($result['resultCode'] == self::TX_TER_RESULT_EXTENSIONKEYNOTVALID) {
            throw new ExtensionKeyNotValidException(sprintf(
                '"%s" is not a valid extension key',
                $extensionKey
            ));
        } else {
            throw new \RuntimeException(sprintf('Soap API responded with an unknown response. result code "%s"', $result['resultCode']));
        }
    }
}