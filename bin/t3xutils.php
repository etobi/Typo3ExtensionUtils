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
// disabled, because they don't work yet
//	new Command\Self\CheckUpdateCommand(),
//	new Command\Self\UpdateCommand(),
    new Command\T3x\CreateCommand(),
    new Command\T3x\ExtractCommand(),
    new Command\Ter\FetchCommand(),
    new Command\Ter\InfoCommand(),
    new Command\Ter\UpdateInfoCommand(),
    new Command\Ter\UploadCommand(),
    new Command\Ter\PingCommand(),
    new Command\Ter\LoginCommand(),
    new Command\EmConf\UpdateCommand(),
    new Command\Ter\CheckExtensionKeyCommand(),
    new Command\Ter\RegisterExtensionKeyCommand(),
	new Command\Ter\DeleteExtensionKeyCommand(),
	new Command\Ter\SearchAllCommand(),
	new Command\Ter\SearchUserCommand(),
	new Command\Ter\SearchExtensionKeyCommand(),
));

// remove the option --version. We need this for extension handling
$options = $console->getDefinition()->getOptions();
unset($options['version']);
$console->getDefinition()->setOptions($options);

$console->run();