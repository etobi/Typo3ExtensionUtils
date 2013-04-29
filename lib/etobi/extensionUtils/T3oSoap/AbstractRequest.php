<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * a request object that queries the TYPO3.org SOAP API
 */
abstract class AbstractRequest {

    /**
     * @var string
     */
    protected $wsdlURL = 'http://www.latest.dev.t3o.typo3.org/wsdl/tx_ter_wsdl.php';

    /**
     * @var null|Client
     */
    protected $client = NULL;

    /**
     * @param string $wsdlURL
     */
    public function setWsdlURL($wsdlURL)
    {
        $this->wsdlURL = $wsdlURL;
    }

    /**
     * @param \etobi\extensionUtils\T3oSoap\Client|null $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return \etobi\extensionUtils\T3oSoap\Client|null
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * create a new client
     *
     * @return Client
     */
    protected function createClient() {
        $this->client = new Client($this->wsdlURL);
        return $this->client;
    }
}