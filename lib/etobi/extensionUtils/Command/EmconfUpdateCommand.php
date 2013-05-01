<?php

namespace etobi\extensionUtils\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use etobi\extensionUtils\Service\T3xFile;

/**
 * EmconfUpdateCommand updates information in an ext_emconf.php file
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class EmconfUpdateCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('emconf:update')
            ->setDefinition(array(
                new InputArgument('emconfFile', InputArgument::REQUIRED, 'path to the ext_emconf.php file'),
                new InputOption('title', NULL, InputOption::VALUE_REQUIRED, 'title string to set'),
                new InputOption('description', NULL, InputOption::VALUE_REQUIRED, 'description string to set'),
                new InputOption('author', NULL, InputOption::VALUE_REQUIRED, 'author string to set'),
                new InputOption('email', NULL, InputOption::VALUE_REQUIRED, 'email string to set'),
                new InputOption('company', NULL, InputOption::VALUE_REQUIRED, 'company string to set'),
                new InputOption('version', NULL, InputOption::VALUE_REQUIRED, 'version string to set'),
                new InputOption('state', NULL, InputOption::VALUE_REQUIRED, 'state string to set'),
            ))
            ->setDescription('Update an ext_emconf.php file')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    protected function prepareParameters(InputInterface $input, OutputInterface $output) {
        if(!is_file($input->getArgument('emconfFile'))) {
            $this->logger->debug(sprintf('"%s" is not a file', $input->getArgument('emconfFile')));
            $emconfFileName = rtrim($input->getArgument('emconfFile'), '/') . DIRECTORY_SEPARATOR . 'ext_emconf.php';
            if(!is_file($emconfFileName)) {
                $this->logger->critical(sprintf(
                    'Neither "%s" nor "%s" is a file.',
                    $input->getArgument('emconfFile'),
                    $emconfFileName
                ));
                return 1;
            } else {
                $input->setArgument('emconfFile', $emconfFileName);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $emconf = new \etobi\extensionUtils\Service\EmConf();
        $emconf->readFile($input->getArgument('emconfFile'));

        if($input->getOption('title')) {
            $emconf->setTitle($input->getOption('title'));
            $this->logger->info(sprintf('title set to "%s"', $input->getOption('title')));
        }
        if($input->getOption('description')) {
            $emconf->setDescription($input->getOption('description'));
            $this->logger->info(sprintf('description set to "%s"', $input->getOption('description')));
        }
        if($input->getOption('author')) {
            $emconf->setAuthor($input->getOption('author'));
            $this->logger->info(sprintf('author set to "%s"', $input->getOption('author')));
        }
        if($input->getOption('email')) {
            $emconf->setAuthorEmail($input->getOption('email'));
            $this->logger->info(sprintf('email set to "%s"', $input->getOption('email')));
        }
        if($input->getOption('company')) {
            $emconf->setAuthorCompany($input->getOption('company'));
            $this->logger->info(sprintf('company set to "%s"', $input->getOption('company')));
        }
        if($input->getOption('version')) {
            $emconf->setVersion($input->getOption('version'));
            $this->logger->info(sprintf('version set to "%s"', $input->getOption('version')));
        }
        if($input->getOption('state')) {
            $emconf->setState($input->getOption('state'));
            $this->logger->info(sprintf('state set to "%s"', $input->getOption('state')));
        }

        $success = $emconf->writeFile();
        if($success) {
            $output->writeln(sprintf('"%s" updated', $input->getArgument('emconfFile')));
        }
    }
}
