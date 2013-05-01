<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\ConsoleUtility\ConsoleOutputLoggerProxy;
use Symfony\Component\Console\Input\InputOption;
use Psr\Log\LoggerInterface;
use etobi\extensionUtils\ConsoleUtility\FileSizeProgressBar;

/**
 * @author Christian Zenker <christian.zenker@599media.de>
 */
abstract class AbstractTerCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configureSoapOptions()
    {
        $this->addOption('wsdl', NULL, InputOption::VALUE_REQUIRED, 'wsdl url for the SOAP uri');
    }

    /**
     * poor man's Dependency Injection
     *
     * @param $className
     * @throws \InvalidArgumentException
     * @return \etobi\extensionUtils\T3oSoap\AbstractRequest
     */
    public function getRequestObject($className) {
        if(!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('The class "%s" does not exist.', $className));
        }
        $object = new $className();
        if(!($object instanceof \etobi\extensionUtils\T3oSoap\AbstractRequest)) {
            throw new \InvalidArgumentException(sprintf('expected class %s to be a \\etobi\\extensionUtils\\T3oSoap\\AbstractRequest, but it is not', $className));
        }

        if($this->input->getOption('wsdl') || $this->getConfigurationValue('ter.wsdl')) {
            $wsdl = $this->input->getOption('wsdl') ?: $this->getConfigurationValue('ter.wsdl');
            $object->setWsdlURL($wsdl);
            $this->logger->debug(sprintf('set "%s" as wsdl url', $wsdl));
        } else {
            $this->logger->debug(sprintf('use "%s" as wsdl url', $object->getWsdlURL()));
        }

        if($object instanceof \etobi\extensionUtils\T3oSoap\AbstractAuthenticatedRequest) {
            $object->setCredentials(
                $this->input->getOption('username'),
                $this->input->getOption('password')
            );
            $this->logger->debug(sprintf('set username "%s" and password', $this->input->getOption('username')));
        }

        return $object;
    }
}
