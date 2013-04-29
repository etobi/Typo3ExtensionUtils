<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * issues a simple "Hello World" request to the SOAP API
 */
class PingRequest extends AbstractRequest {

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