<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * check if the given credentials are valid
 */
class LoginRequest extends AbstractAuthenticatedRequest {

    /**
     * check if the given credentials are valid
     *
     * @param null|string $username
     * @param null|string $password
     * @return bool
     */
    public function checkCredentials($username = NULL, $password = NULL)
    {
        if($username && $password) {
            $this->setCredentials($username, $password);
        }

        $this->createClient();
        $result = $this->client->call('login');

        return $result;
    }
}