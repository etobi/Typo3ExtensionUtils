<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

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
            ->setDescription('Delete a given extension key on typo3.org')
            //@TODO: longer help text
//            ->setHelp()
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
        $checkRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\DeleteExtensionKeyRequest');
        try {
            $result = $checkRequest->deleteExtensionKey($extensionKey);

            if($result) {
                $output->writeln(sprintf('"%s" successfully deleted', $extensionKey));
                return 0;
            } else {
                $output->writeln(sprintf('<error>"%s" could not be deleted</error>', $extensionKey));
                return 1;
            }
        } catch (\etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
            return 1;
        }
    }
}
