<?php

namespace etobi\extensionUtils\Command;

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
class TerLoginCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:login-test')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::OPTIONAL, 'Your username at typo3.org'),
                new InputOption('password', 'p', InputOption::VALUE_REQUIRED, 'Your password at typo3.org'),
            ))
            ->setDescription('Checks if a username and password are valid credentials for TYPO3 SOAP API')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareParameters(InputInterface $input, OutputInterface $output) {
        // username
        if(!$input->getArgument('username')) {
            if($this->getConfigurationValue('ter.username')) {
                $username = $this->getConfigurationValue('ter.username');
                $this->logger->debug(sprintf('use username "%s" from configuration file', $username));
            } else {
                $username = $this->getDialogHelper()->ask(
                    $output,
                    '<question>Username on typo3.org:</question> '
                );
                $this->logger->debug(sprintf('interactively asked user. "%s" given', $username));
            }
            $input->setArgument('username', $username);
        }

        // password
        if(!$input->getOption('password')) {
            if($this->getConfigurationValue('ter.password')) {
                $password = $this->getConfigurationValue('ter.password');
                $this->logger->debug('use password from configuration file');
            } else {
                $password = $this->getDialogHelper()->askHiddenResponse(
                    $output,
                    sprintf('<question>Password [%s]:</question> ', $input->getArgument('username'))
                );
                $this->logger->debug('interactively asked user for password');
            }
            $input->setOption('password', $password);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loginRequest = new \etobi\extensionUtils\T3oSoap\LoginRequest();
        $result = $loginRequest->checkCredentials(
            $input->getArgument('username'),
            $input->getOption('password')
        );

        if($result) {
            $output->writeln('Your credentials are valid.');
            return 0;
        } else {
            $output->writeln('<error>Your credentials are invalid.</error>');
            return 1;
        }
    }
}
