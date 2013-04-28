<?php

use etobi\extensionUtils\Command as Command;

require(__DIR__ . '/../lib/autoload.php');
\etobi\extensionUtils\register_autoload();

$console = new \Symfony\Component\Console\Application();
$console->setName('t3xutils');
$console->setVersion(defined('T3XUTILS_VERSION') ? constant('T3XUTILS_VERSION') : '?');
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