<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Proxy\ConsoleOutputLoggerProxy;

/**
 * UploadCommand uploads an extension into TER
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class UploadCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('upload')
            ->setDefinition(array(
                new InputArgument('username', InputArgument::REQUIRED, 'Your username at typo3.org'),
                new InputArgument('password', InputArgument::REQUIRED, 'Your password at typo3.org'),
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'The extension key you want to upload an extension for'),
                new InputArgument('uploadComment', InputArgument::REQUIRED, 'Brief description what has changed with this version'),
                new InputArgument('pathToExtension', InputArgument::REQUIRED, 'the path to the extension on your local file system'),
            ))
            ->setDescription('Upload an extension to TER')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $upload = new \etobi\extensionUtils\ter\TerUpload();
        $upload->setExtensionKey($input->getArgument('extensionKey'))
            ->setUsername($input->getArgument('username'))
            ->setPassword($input->getArgument('password'))
            ->setUploadComment($input->getArgument('uploadComment'))
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
