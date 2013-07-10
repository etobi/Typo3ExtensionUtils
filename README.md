# TYPO3 Extension Utils

This is a collection of CLI utilities for TYPO3 CMS Extension. Goal is to be able to do common tasks
while developing extensions from the cli. All tools work without a fully functional TYPO3 installation. Actually the
TYPO3 CMS core isn't needed at all for this utilities.

The library is useful for automatic scripting as well as manual execution.

## Installation with git

	git clone --recursive https://github.com/etobi/Typo3ExtensionUtils.git
	cd Typo3ExtensionUtils/bin/
	chmod +x t3xutils.phar
	./t3xutils.php list

## Features

* √ Upload an extension by given path to the TER (TYPO3 Extension Repository)
* √ display extension and version information from TER
* √ download an extension from TER as t3x
* √ Extract extension from a .t3x file
* √ Create a .t3x from a extension path
* √ (new in 2.0) edit ext_emconf.php from the command line
* √ (new in 2.0) register/delete extension key for TER
* √ (new in 2.0) show username for extensions without versions
* √ (new in 2.0) interactive questions when you have not entered all parameters
* √ (new in 2.0) store username or password in a file for easy scripting
* (planned) Update the MD5 sums in ext_emconf.php
* (planned) Check extension files against MD5 sums to find modified files


## Getting started

Download the latest version of an extension and unpack it

	./t3xutils.php ter:fetch -x my_extension

Create a t3x file from an extension folder

	./t3xutils.php t3x:create my_extension

Upload a new version of an extension

	./t3xutils.php emconf:update --version="1.2.3" my_extension
	./t3xutils.php ter:upload my_extension

List all versions of an extension

	./t3xutils.php ter:info my_extension

Get information on the last version of an extension

	./t3xutils.php ter:info --latest my_extension


## Help

To show a list of all available commands just run the program without any parameters

	./t3xutils.php

To show help on a command and all available arguments and options

	./t3xutils.php help [command]

## .t3xuconfig

To allow easy scripting you can create a `.t3xuconfig` file with default settings for some parameters.
The file has to be in the folder where you execute the command.

The following parameters are currently supported and unless you override them with the command call:

* ter.username: Username on typo3.org
* ter.password: Password on typo3.org
* ter.wsdl:     Url for SOAP API

## Tipps

* The verbose option `-v` shows you some more information on what happens in the background. Adding it multiple times,
  like `-vv` will show you even more debugging output.

* Most commands ask you for further information if you missed to enter some parameter.

* You don't have to type the full command if there is only one possible match. Each of the
  following lines executes the same command:

    ./t3xutils.php ter:register-key my_extension
    ./t3xutils.php ter:register my_extension
    ./t3xutils.php te:r my_extension

  Of course you should not make use of this when writing scripts you want to execute automatically, because they might
  fail when new commands are added in the future.

* You can mix options and arguments as long as you keep the order of the options. Each of the following lines executes
  the same command:
    ./t3xutils.php ter:register-key --username="john.doe" my_extension
    ./t3xutils.php ter:register-key --username "john.doe" my_extension
    ./t3xutils.php ter:register-key my_extension -u "john.doe"

## Stability

We won't change the console API in a way that it produces different output than in older versions. So it is perfectly fine
for you to use the console API in your automated build tools.

Internals on the other hand might change. It is not recommended to use the PHP classes directly. We want to make the
PHP classes available in the future, but right now it is not recommended to use them if you are fine with adapting your
code later on.

## Contribution

* Git Repository: https://github.com/etobi/Typo3ExtensionUtils
* Issue Tracker: https://github.com/etobi/Typo3ExtensionUtils/issues
