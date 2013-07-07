<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotExistsException;
use etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerDeleteExtensionKeyCommand deletes an extension key without any uploads
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class DeleteExtensionKeyCommand extends AbstractAuthenticatedTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:delete-key')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::OPTIONAL, 'the extension key to delete'),
            ))
            ->setDescription('Delete a given extension key')
            ->setHelp(<<<EOT
Delete an extension key without any uploads.

After deleting the key is available for registration by anyone again.
You can not delete extension keys that have uploaded versions.

Example
=======

Delete extension key "my_extension"

  t3xutils ter:delete-key my_extension

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
        if(!$input->getArgument('extensionupdateinfoKey')) {

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
	    /** @var \etobi\extensionUtils\T3oSoap\DeleteExtensionKeyRequest $checkRequest */
	    $checkRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\DeleteExtensionKeyRequest');
        try {
            $checkRequest->deleteExtensionKey($extensionKey);

            $output->writeln(sprintf('"%s" successfully deleted', $extensionKey));
            return 0;
        } catch(ExtensionKeyNotExistsException $e) {
	        $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
	        return 1;
        } catch (ExtensionKeyNotValidException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return 2;
        }
    }
}
