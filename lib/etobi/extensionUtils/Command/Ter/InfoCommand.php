<?php

namespace etobi\extensionUtils\Command\Ter;

use etobi\extensionUtils\Command\AbstractCommand;
use etobi\extensionUtils\Controller\TerController;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * InfoCommand shows information on an extension
 *
 * @author Christian Zenker <christian.zenker@599media.de>
 */
class InfoCommand extends AbstractCommand
{

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ter:info')
            ->setDefinition(array(
                new InputArgument('extensionKey', InputArgument::REQUIRED, 'the extension key you want information on'),
                new InputArgument('version', InputArgument::OPTIONAL, 'get information on an specific version'),
                new InputOption('latest', 'l', InputOption::VALUE_NONE, 'get information on the latest version if non is specified'),
                new InputOption('width', NULL, InputOption::VALUE_OPTIONAL, 'maximum display width in columns', 80),
            ))
            ->setDescription('Get information about an extension')
            //@TODO: longer help text
//            ->setHelp()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $extensionKey = $input->getArgument('extensionKey');
        $version = $input->getArgument('version');

	    $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
	    try {
		    $extensionsXmlService->isFileValid();
	    } catch(\InvalidArgumentException $e) {
		    $this->logger->info('Information file is not yet loaded. Fetch it.');

		    $command = $this->getApplication()->find('ter:update-info');
		    $arguments = array(
			    'command' => 'ter:update-info',
		    );
		    $updateInfoInput = new ArrayInput($arguments);
		    $command->run($updateInfoInput, $output);
	    }

        if(!$version && $input->getOption('latest')) {
            $version = $extensionsXmlService->findLatestVersion($extensionKey);
            $input->setArgument('version', $version);
            $this->logger->info(sprintf('Latest version of %s is %s', $extensionKey, $version));
        }

        if($version) {
            return $this->executeVersionInfo($input, $output);
        } else {
            return $this->executeExtensionInfo($input, $output);
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return integer
     */
    protected function executeExtensionInfo(InputInterface $input, OutputInterface $output) {
        $extensionKey = $input->getArgument('extensionKey');

        $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
        $versionInfos = $extensionsXmlService->getExtensionInfo($extensionKey);

        $this->printExtensionInfo($versionInfos);

        return 0;
    }

    /**
     * print extension information into the output
     *
     * @param array $versions
     */
    protected function printExtensionInfo(array $versions) {
        $maxVersionStrlen = strlen('version');
        $maxDateStrlen = 19;

        foreach($versions as $version) {
            $strlen = strlen($version['version']);
            if($strlen > $maxVersionStrlen) {
                $maxVersionStrlen = $strlen;
            }
        }

        $maxCommentStrlen = max($this->input->getOption('width') - $maxVersionStrlen - $maxDateStrlen - 4, 1);

        $this->logger->debug('width of version column is ' . $maxVersionStrlen);
        $this->logger->debug('width of date column is ' . $maxDateStrlen);
        $this->logger->debug('width of comment column is ' . $maxCommentStrlen);


        $lineFormat = '%-' . $maxVersionStrlen . 's  %-' . $maxDateStrlen . 's  %-' . $maxCommentStrlen . 's';

        // print header
        $this->output->writeln(sprintf(
            $lineFormat,
            'version',
            'uploaded at',
            'comment'
        ));

        // print data
        foreach($versions as $version) {
            $comment = explode("\n", wordwrap(trim($version['comment']), $maxCommentStrlen));

            // print first line
            $this->output->writeln(sprintf(
                $lineFormat,
                $version['version'],
                date('d.m.Y H:i:s', $version['timestamp']),
                array_shift($comment)
            ));
            while($row = array_shift($comment)) {
                $this->output->writeln(sprintf(
                    $lineFormat,
                    '',
                    '',
                    $row
                ));
            }
        }
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return integer
     */
    protected function executeVersionInfo(InputInterface $input, OutputInterface $output) {
        $extensionKey = $input->getArgument('extensionKey');
        $version = $input->getArgument('version');

        $extensionsXmlService = new \etobi\extensionUtils\Service\ExtensionsXml();
        $infos = $extensionsXmlService->getVersionInfo($extensionKey, $version);

        $this->printVersionInfo($infos);

        return 0;
    }

    /**
     * print extension information into the output
     *
     * @param array $version
     * @return void
     */
    protected function printVersionInfo(array $version) {
        $maxKeyStrlen = 1;

        foreach($version as $key=>$value) {
            $strlen = strlen($key);
            if($strlen > $maxKeyStrlen) {
                $maxKeyStrlen = $strlen;
            }
        }

        $maxValueStrlen = max($this->input->getOption('width') - $maxKeyStrlen - 2, 1);

        $this->logger->debug('width of key column is ' . $maxKeyStrlen);
        $this->logger->debug('width of value column is ' . $maxValueStrlen);


        $lineFormat = '%-' . $maxKeyStrlen . 's  %-' . $maxValueStrlen . 's';

        // print data
        foreach($version as $key=>$value) {
            $methodName = 'convert' . ucfirst($key);
            if(method_exists($this, $methodName)) {
                $value = $this->$methodName($value, $maxValueStrlen);

            } else {
                $value = wordwrap(trim($value), $maxValueStrlen);
            }
            if(!is_array($value)) {
                $value = explode("\n", $value);
            }

            // print first line
            $this->output->writeln(sprintf(
                $lineFormat,
                $key,
                array_shift($value)
            ));
            while($row = array_shift($value)) {
                $this->output->writeln(sprintf(
                    $lineFormat,
                    '',
                    $row
                ));
            }
        }
    }

    /**
     * @param $timestamp
     * @return string
     */
    protected function convertLastuploaddate($timestamp) {
        return date('d.m.Y H:i:s', $timestamp);
    }

    /**
     * @param array $dependencies
     * @param int $maxLength
     * @return array
     */
    protected function convertDependencies($dependencies, $maxLength = 80) {
        $maxKindStrlen = 0;
        $maxKeyStrlen = 0;

        foreach($dependencies as $dependency) {
            $strlen = strlen($dependency['kind']);
            if($strlen > $maxKindStrlen) {
                $maxKindStrlen = $strlen;
            }

            $strlen = strlen($dependency['extensionKey']);
            if($strlen > $maxKeyStrlen) {
                $maxKeyStrlen = $strlen;
            }
        }

        $maxVersionStrlen = max($maxLength - 4 - $maxKindStrlen - $maxKeyStrlen, 1);
        $lineFormat = '<comment>%-' . $maxKindStrlen . 's</comment>  %-' . $maxKeyStrlen . 's  <comment>%-' . $maxVersionStrlen . 's</comment>';

        $return = array();
        foreach($dependencies as $dependency) {
            $return[] = sprintf(
                $lineFormat,
                $dependency['kind'],
                $dependency['extensionKey'],
                $dependency['versionRange'] ? '(' . $dependency['versionRange'] . ')' : ''
            );
        }
        return $return;
    }
}
