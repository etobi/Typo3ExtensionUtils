<?php

namespace etobi\extensionUtils\T3oSoap;

class Client {

    protected $wsdlURL = 'http://www.latest.dev.t3o.typo3.org/wsdl/tx_ter_wsdl.php';

    protected $soapOptions = array(
        'trace' => TRUE,
        'exceptions' => TRUE
    );

    protected $data = array();

    protected $functionName = NULL;

    protected $arguments = array();

    public function call($functionName = NULL, $arguments = array()) {
        if($functionName) {
            $this->setFunctionName($functionName);
        }
        if(!empty($arguments)) {
            $this->addArguments($arguments);
        }
        $soapClient = $this->getSoapClient();
        $return = $soapClient->__call($this->functionName, $this->arguments);

        return $return;
    }

    protected function getSoapClient() {
        return new \SoapClient($this->wsdlURL, $this->soapOptions);
    }

    public function setFunctionName($functionName)
    {
        $this->functionName = $functionName;
    }

    public function addArgument($name, $value) {
        $this->arguments[$name] = $value;
    }

    public function addArguments($arguments) {
        $this->arguments = array_merge($this->arguments, $arguments);
    }


}