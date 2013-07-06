<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Controller\SelfController;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * TerPingCommand checks SOAP API connectivity
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class PingCommand extends AbstractTerCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:ping')
            ->setDefinition(array())
            ->setDescription('Check SOAP API connectivity')
            //@TODO: longer help text
//            ->setHelp()
        ;
        $this->configureSoapOptions();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pingRequest = $this->getRequestObject('\\etobi\\extensionUtils\\T3oSoap\\PingRequest');
        if($pingRequest->isApiWorking()) {
            $output->writeln('The API is working.');
            return 0;
        } else {
            $output->writeln('<error>The API is not working.</error>');
            return 1;
        }
    }
}
