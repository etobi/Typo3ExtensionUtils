<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * a request object that queries the TYPO3.org SOAP API with user authentication
 */
abstract class AbstractAuthenticatedRequest extends AbstractRequest {

    /**
     * @var null|string
     */
    protected $username = NULL;

    /**
     * @var null|string
     */
    protected $password = NULL;

    /**
     * set credentials for the API call
     *
     * @param string $username
     * @param string $password
     */
    public function setCredentials($username = NULL, $password = NULL) {
        $this->username = $username;
        $this->password = $password;

        if($this->client) {
            $this->assignCredentialsToClient();
        }
    }

    /**
     * create a new client
     *
     * @return Client
     */
    protected function createClient() {
        $this->client = new Client($this->wsdlURL);
        if($this->hasCredentials()) {
            $this->assignCredentialsToClient();
        }
        return $this->client;
    }

    /**
     * if this Request has been given credentials
     * @return bool
     */
    protected function hasCredentials() {
        return !is_null($this->username) && !is_null($this->password);
    }

    /**
     * apply the credentials to this SOAP API call
     */
    protected function assignCredentialsToClient() {
        $this->client->addArgument('accountData', array(
            'username' => $this->username,
            'password' => $this->password
        ));
    }
}