<?php

use etobi\extensionUtils\Command as Command;

require(__DIR__ . '/../lib/autoload.php');
\etobi\extensionUtils\register_autoload();

$console = new \Symfony\Component\Console\Application();
$console->setName('t3xutils');
$console->setVersion(defined('T3XUTILS_VERSION') ? constant('T3XUTILS_VERSION') : '?');

// add a helper set that handles configuration
$config = new \etobi\extensionUtils\ConsoleHelper\ConfigHelper();
// @todo more formats, configurable location
$configFileName = __DIR__ . '/../config.ini';
if(file_exists($configFileName) && is_readable($configFileName)) {
    $config->mergeConfiguration(parse_ini_file($configFileName, true));
}
$console->getHelperSet()->set($config);

// add all available commands
$console->addCommands(array(
    new Command\CheckForUpdateCommand(),
    new Command\CreateCommand(),
    new Command\ExtractCommand(),
    new Command\FetchCommand(),
    new Command\InfoCommand(),
    new Command\SelfUpdateCommand(),
    new Command\UpdateInfoCommand(),
    new Command\UploadCommand(),
    new Command\TerPingCommand(),
));


$console->run();