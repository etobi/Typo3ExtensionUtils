# TYPO3 Extension Utils

This is (or should get) a collection of CLI utilities for TYPO3 CMS Extension. Goal is to be able to do common tasks
while developing extensions from the cli. All tools work without a fully functional TYPO3 installation. Actually the
TYPO3 CMS core isn't needed at all for this utilities.


## Installation

	wget http://bit.ly/t3xutils -O t3xutils.phar
	chmod +x t3xutils.phar
	./t3xutils.phar help

## Features

* √ "check for updates" and "selfupdate"
* √ Upload an extension by given path to the TER (TYPO3 Extension Repository)
* √ display extension and version informations from TER
* √ download an extension from TER as t3x
* (coming) Delete an extension in a certain version from TER
* (coming) Update the MD5 sums in ext_emconf.php
* √ Extract extension from a .t3x file
* (coming) Create a .t3x from a extension path


### Help

Usage:

	./bin/t3xutils.phar help


### Self-update

	# check if a new version is available
	./bin/t3xutils.phar checkforupdate

	# download and install the new version
	./bin/t3xutils.phar selfupdate


### Upload Extension to TER

Usage:

	./bin/t3xutils.phar <typo3.org-username> <typo3.org-password> <extensionKey> "<uploadComment>" <pathToExtension>

Example:

	./bin/t3xutils.phar eTobi.de 'mySecretPassword' foobar "Minor Bugfixes and cleanup" /var/www/foobar/typo3conf/ext/foobar/


### Display TER informations about an extension

	# fetch and cache TER extension metadata
	./bin/t3xutils.phar updateinfo

	# Display available versions of EXT:foobar
	./bin/t3xutils.phar info foobar

	# Display metadata of EXT:foobar, version 1.2.3
	./bin/t3xutils.phar info foobar 1.2.3


### Fetch extension from TER

	# download EXT:foobar in version 1.2.3 as .t3x file from TER
	./bin/t3xutils.phar fetch foobar 1.2.3
