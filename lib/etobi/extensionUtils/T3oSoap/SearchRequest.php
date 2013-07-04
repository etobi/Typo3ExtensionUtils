<?php

namespace etobi\extensionUtils\T3oSoap;

/**
 * search for extension keys
 */
class SearchRequest extends AbstractAuthenticatedRequest {

    public function search($username = NULL, $extensionKey = NULL, $title = NULL, $description = NULL)
    {
	    $extensionKeyFilterOptions = new \stdClass();
	    if($username) {
		    $extensionKeyFilterOptions->username = $username;
	    }
	    if($extensionKey) {
		    $extensionKeyFilterOptions->extensionKey = $extensionKey;
	    }
	    if($title) {
		    $extensionKeyFilterOptions->title = $title;
	    }
	    if($description) {
		    $extensionKeyFilterOptions->description = $description;
	    }

	    $this->createClient();
	    $this->client->addArgument($extensionKeyFilterOptions);

	    $response = $this->client->call('getExtensionKeys');
	    if($response['simpleResult']['resultCode'] !== self::TX_TER_RESULT_GENERAL_OK) {
		    throw new \RuntimeException(sprintf('Soap API responded with an unknown response. result code "%s"', $response['simpleResult']['resultCode']));
	    }

	    return $response['extensionKeyData'];
    }

	public function searchByUsername($username) {
		return $this->search($username, NULL, NULL, NULL);
	}

	public function searchByExtensionKey($extensionKey) {
		return $this->search(NULL, $extensionKey, NULL, NULL);
	}

	public function searchByTitle($title) {
		return $this->search(NULL, NULL, $title, NULL);
	}

	public function searchByDescription($description) {
		return $this->search(NULL, NULL, NULL, $description);
	}
}