<?php

namespace etobi\extensionUtils\Command;

use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerCheckExtensionKeyCommand checks if a given extension key is available
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class TerCheckExtensionKeyCommand extends AbstractAuthenticatedTerCommand
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
            ->setDescription('Checks if a given extension key is available for registration')
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
        } catch (\etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException $e) {
            $output->writeln(sprintf('<error>"%s" is not formally valid as extension key and cannot be registered</error>', $extensionKey));
            return 1;
        }
    }
}
