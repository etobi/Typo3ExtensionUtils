<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerPingCommand checks SOAP API connectivity
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class PingCommand extends AbstractTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:ping')
            ->setDefinition(array())
            ->setDescription('Check SOAP API connectivity')
            ->setHelp(<<<EOT
Check if the SOAP Api is responding.

This command can be helpful to check if your customly configured TER repository is available.

This command does not need any credentials.

Example
=======

Check if the main repository on typo3.org is available

  t3xutils ter:ping

Check a custom repository on example.org

  t3xutils ter:ping --wsdl="http://example.org/wsdl/tx_ter_wsdl.php"

.t3xuconfig
===========

* <info>ter.wsdl</info>: wsdl url for the Soap API
EOT
)
        ;
        $this->configureSoapOptions();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
	    /** @var \etobi\extensionUtils\T3oSoap\PingRequest $pingRequest */
        $pingRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\PingRequest');
        if($pingRequest->isApiWorking()) {
            $output->writeln('The API is working.');
            return 0;
        } else {
            $output->writeln('<error>The API is not working.</error>');
            return 1;
        }
    }
}
