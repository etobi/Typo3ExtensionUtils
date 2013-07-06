<?php

namespace etobi\extensionUtils\Command\Ter;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * UploadCommand uploads an extension into TER
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class UploadCommand extends AbstractAuthenticatedTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:upload')
            ->setDefinition(array(
                new InputArgument('pathToExtension', InputArgument::REQUIRED, 'the path to the extension on your local file system'),
                new InputArgument('extensionKey', InputArgument::OPTIONAL, 'The extension key you want to upload an extension for'),
                new InputOption('comment', 'c', InputOption::VALUE_REQUIRED, 'Brief description what has changed with this version'),
            ))
            ->setDescription('Upload an extension to TER')
            //@TODO: longer help text
//            ->setHelp()
        ;
        $this->configureSoapOptions();
        $this->configureCredentialOptions();
    }

    protected function prepareParameters(InputInterface $input, OutputInterface $output)
    {
        // make sure credentials are set
        $this->prepareCredentialOptions($input, $output);

        if(!$input->getArgument('extensionKey')) {
            $extensionKey = basename($input->getArgument('pathToExtension'));
            $extensionKey = $this->getDialogHelper()->ask(
                $output,
                sprintf('<question>extension key [%s]:</question> ', $extensionKey),
                $extensionKey
            );
            $this->logger->debug(sprintf('interactively asked for extensionKey. "%s" given', $extensionKey));
            $input->setArgument('extensionKey', $extensionKey);
        }

        if(!$input->getOption('comment')) {
            $comment = $this->getDialogHelper()->ask(
                $output,
                '<question>upload comment:</question> '
            );
            $this->logger->debug(sprintf('interactively asked for upload comment. "%s" given', $comment));
            $input->setOption('comment', $comment);
        }

    }


    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $upload = new \etobi\extensionUtils\ter\TerUpload();
        $upload->setExtensionKey($input->getArgument('extensionKey'))
            ->setUsername($input->getOption('username'))
            ->setPassword($input->getOption('password'))
            ->setUploadComment($input->getOption('comment'))
            ->setPath($input->getArgument('pathToExtension'));

        try {
            $response = $upload->execute();
        } catch (\SoapFault $s) {
            $this->logger->error('SOAP-Error: ' . $s->getMessage());
            return 1;
        } catch(\Exception $e) {
            $this->logger->error('Error: ' . $e->getMessage());
            return 1;
        }

        if (!is_array($response)) {
            $this->logger->error('Error: ' . $response);
            return 1;
        }
        if ($response['resultCode'] == 10504) {
            if(is_array($response['resultMessages'])) {
                $output->writeln($response['resultMessages']);
            }
            return 0;
        }
        return 0;
    }
}
