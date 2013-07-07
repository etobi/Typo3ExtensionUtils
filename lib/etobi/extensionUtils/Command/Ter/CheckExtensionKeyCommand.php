<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerCheckExtensionKeyCommand checks if a given extension key is available
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class CheckExtensionKeyCommand extends AbstractAuthenticatedTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:check-key')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::OPTIONAL, 'the extension key to check'),
            ))
            ->setDescription('Check if a given extension key is available for registration')
            ->setHelp(<<<EOT
Check if a given extension key is a valid extension key and is available for registration.

This also finds extension keys that don't have any uploads and therefor can't
be found on typo3.org.

Example
=======

Check if the extension key "my_extension" is valid and available for registration

  t3xutils ter:check-key my_extension

config.ini
==========

* <info>ter.username</info>: username on typo3.org
* <info>ter.password</info>: password on typo3.org
* <info>ter.wsdl</info>: wsdl url for the Soap API

Return codes
============

* `0` if the key is available for registration
* `1` if the key is already registered
* `2` if the key is formally invalid
EOT
)
        ;
        $this->configureSoapOptions();
        $this->configureCredentialOptions();
    }

    protected function prepareParameters(InputInterface $input, OutputInterface $output)
    {
        parent::prepareParameters($input, $output);
        if(!$input->getArgument('extensionKey')) {

            $extensionKey = $this->getDialogHelper()->ask(
                $output,
                '<question>extension key:</question> '
            );
            $this->logger->debug(sprintf('interactively asked for extension key. "%s" given', $extensionKey));
            $input->setArgument('extensionKey', $extensionKey);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionKey = $input->getArgument('extensionKey');
	    /** @var \etobi\extensionUtils\T3oSoap\CheckExtensionKeyRequest $checkRequest */
        $checkRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\CheckExtensionKeyRequest');
        try {
            $result = $checkRequest->checkExtensionKey($extensionKey);

            if($result) {
                $output->writeln(sprintf('"%s" is available for registration', $extensionKey));
                return 0;
            } else {
                $output->writeln(sprintf('<error>"%s" is already taken</error>', $extensionKey));
                return 1;
            }
        } catch (ExtensionKeyNotValidException $e) {
            $output->writeln(sprintf('<error>"%s" is not formally valid as extension key and cannot be registered</error>', $extensionKey));
            return 2;
        }
    }
}
