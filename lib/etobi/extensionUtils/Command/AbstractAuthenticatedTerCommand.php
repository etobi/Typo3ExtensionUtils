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
abstract class AbstractAuthenticatedTerCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function setCredentialOptions()
    {
        $this->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'Your username at typo3.org');
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED, 'Your password at typo3.org');
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareParameters(InputInterface $input, OutputInterface $output) {
        $this->prepareCredentialOptions($input, $output);
    }

    protected function prepareCredentialOptions(InputInterface $input, OutputInterface $output) {
        // username
        if(!$input->getOption('username')) {
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
            $input->setOption('username', $username);
        }

        // password
        if(!$input->getOption('password')) {
            if($this->getConfigurationValue('ter.password')) {
                $password = $this->getConfigurationValue('ter.password');
                $this->logger->debug('use password from configuration file');
            } else {
                $password = $this->getDialogHelper()->askHiddenResponse(
                    $output,
                    sprintf('<question>Password [%s]:</question> ', $input->getOption('username'))
                );
                $this->logger->debug('interactively asked user for password');
            }
            $input->setOption('password', $password);
        }
    }

}
