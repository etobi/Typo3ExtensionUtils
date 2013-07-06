<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerLoginCommand checks if a username and password are valid credentials for TYPO3 SOAP API
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class LoginCommand extends AbstractAuthenticatedTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:login-test')
            ->setDefinition(array())
            ->setDescription('Checks if a username and password are valid credentials for TYPO3 SOAP API')
            ->setHelp(<<<EOT
Check if your given username and password are valid credentials for typo3.org.

Some commands form the ter:* namespace require a valid login on typo3.org.
This command checks if your given credentials are valid.

Example
=======

Interactively ask for your username and password and check if they are valid on typo3.org

  t3xutils ter:login-test
EOT
)
        ;
        $this->configureSoapOptions();
        $this->configureCredentialOptions();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
	    /** @var \etobi\extensionUtils\T3oSoap\LoginRequest $loginRequest */
        $loginRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\LoginRequest');
        $result = $loginRequest->checkCredentials();

        if($result) {
            $output->writeln('Your credentials are valid.');
            return 0;
        } else {
            $output->writeln('<error>Your credentials are invalid.</error>');
            return 1;
        }
    }
}
