<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerRegisterExtensionKeyCommand registers a given extension key
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class RegisterExtensionKeyCommand extends AbstractAuthenticatedTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:register-key')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::OPTIONAL, 'the extension key to register'),
                new InputOption('title', NULL, InputOption::VALUE_REQUIRED, 'title string to set', ''),
                new InputOption('description', NULL, InputOption::VALUE_REQUIRED, 'description string to set', ''),
            ))
            ->setDescription('Registers a given extension key')
            ->setHelp(<<<EOT
Register an extension key

Example
=======

Register extension key "my_extension"

  t3xutils ter:register-key my_extension

Register extension key "my_extension" and set title and description

  t3xutils ter:register-key my_extension --title="Hello World" --description="This is my newest extension"

.t3xuconfig
===========

* <info>ter.username</info>: username on typo3.org
* <info>ter.password</info>: password on typo3.org
* <info>ter.wsdl</info>: wsdl url for the Soap API

Return codes
============

* `0` if the key is registered
* `1` if the key could not be registered
* `2` if the key is formally invalid

EOT
)
        ;
        $this->configureSoapOptions();
        $this->configureCredentialOptions();
    }

    protected function prepareParameters(InputInterface $input, OutputInterface $output)
    {
        if(!$input->getArgument('extensionKey')) {

            $extensionKey = $this->getDialogHelper()->ask(
                $output,
                '<question>extension key:</question> '
            );
            $this->logger->debug(sprintf('interactively asked for extension key. "%s" given', $extensionKey));
            $input->setArgument('extensionKey', $extensionKey);
        }
	    parent::prepareParameters($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionKey = $input->getArgument('extensionKey');
	    /** @var \etobi\extensionUtils\T3oSoap\RegisterExtensionKeyRequest $registerRequest */
        $registerRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\RegisterExtensionKeyRequest');
        try {
            $result = $registerRequest->registerExtensionKey(
                $extensionKey,
                $input->getOption('title'),
                $input->getOption('description')
            );

            if($result) {
                $output->writeln(sprintf('"%s" successfully registered for "%s"', $extensionKey, $input->getOption('username')));
                return 0;
            } else {
                $output->writeln(sprintf('<error>"%s" is already taken</error>', $extensionKey));
                return 1;
            }
        } catch (ExtensionKeyNotValidException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return 2;
        }
    }
}
