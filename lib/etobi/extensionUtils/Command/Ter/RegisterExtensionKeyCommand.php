<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

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
                new InputArgument('extensionKey', InputArgument::OPTIONAL, 'the extension key to check'),
                new InputOption('title', NULL, InputOption::VALUE_REQUIRED, 'title string to set', ''),
                new InputOption('description', NULL, InputOption::VALUE_REQUIRED, 'description string to set', ''),
            ))
            ->setDescription('Registers a given extension key on typo3.org')
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
        $checkRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\RegisterExtensionKeyRequest');
        try {
            $result = $checkRequest->registerExtensionKey(
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
        } catch (\etobi\extensionUtils\T3oSoap\Exception\ExtensionKeyNotValidException $e) {
            $output->writeln(sprintf('<error>"%s" is not formally valid as extension key and cannot be registered</error>', $extensionKey));
            return 1;
        }
    }
}
