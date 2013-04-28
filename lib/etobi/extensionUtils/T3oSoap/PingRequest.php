<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * issues a simple "Hello World" request to the SOAP API
 */
class PingRequest extends AbstractRequest {

    /**
     * @inheritDoc
     */
    public function setCredentials($username = NULL, $password = NULL) {
        throw new \BadMethodCallException('This request does not require credentials.');
    }

    /**
     * check if the SOAP API is working
     *
     * @return bool
     */
    public function isApiWorking()
    {
        $this->createClient();
        $result = $this->client->call('ping', array('value' => 'foobar'));

        return $result === 'pongfoobar';
    }
}