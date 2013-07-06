<?php

namespace etobi\extensionUtils\T3oSoap;
use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException;

/**
 * register an extension key
 */
class RegisterExtensionKeyRequest extends AbstractAuthenticatedRequest {

    /**
     * register a given extension key
     *
     * @param $extensionKey
     * @param string $title
     * @param string $description
     * @throws \RuntimeException
     * @throws Exception\ExtensionKeyNotValidException
     * @return bool
     */
    public function registerExtensionKey($extensionKey, $title='', $description='')
    {
        $extensionKey = (string)$extensionKey;

        $this->createClient();
        $this->client->addArgument(array(
            'extensionKey' => $extensionKey,
            'title' => $title ?: $extensionKey,
            'description' => $description,
        ));

        $result = $this->client->call('registerExtensionKey');

        if($result['resultCode'] == self::TX_TER_RESULT_EXTENSIONKEYSUCCESSFULLYREGISTERED){
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