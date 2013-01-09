<?php
/* **************************************************************
*  Copyright notice
*
*  (c) 1999-2010 Kasper Skårhøj (kasperYYYY@typo3.com)
*  (c) 2006-2010 Karsten Dambekalns <karsten@typo3.org>
*  (c) webservices.nl
*  (c) 2010 Steffen Kamper <steffen@typo3.org>
 * (c) 2012 Tobias Liebig <tobias.liebig@typo3.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

namespace etobi\extensionUtils\ter;

/**
 * Handle upload to TER.
 * Some code snippets are taken from "tx_em_Extensions_Details" and "tx_em_Connection_Ter"
 * from the TYPO3 CMS 4.5 Core.
 */
class TerUpload {

	/**
	 * @var string
	 */
	protected $wsdlURL = 'http://typo3.org/wsdl/tx_ter_wsdl.php';

	/**
	 * @var string
	 */
	protected $username;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $extensionKey;

	/**
	 * @var string
	 */
	protected $uploadComment;

	/**
	 * @var array
	 */
	protected $emConf;

	/**
	 *
	 */
	public function execute() {
		$this->checkRequirements();
		$soap = new Soap();
		$soap->init(array(
			'wsdl' => $this->wsdlURL,
			'soapoptions' => array(
				'trace' => 1,
				'exceptions' => 1
			)
		));
		$response = $soap->call(
			'uploadExtension',
			array(
				'accountData' => $this->getAccountData(),
				'extensionData' => $this->getExtensionData(),
				'filesData' => Helper::getExtensionFilesData($this->path)
			)
		);
		return $response;
	}

	/**
	 * @throws \Exception
	 */
	protected function checkRequirements() {
		if (empty($this->username) || empty($this->password)) {
			throw new \Exception('typo3.org credentials missing.');
		}
		if (empty($this->extensionKey)) {
			throw new \Exception('extension key missing.');
		}
		if (empty($this->uploadComment)) {
			throw new \Exception('upload comment missing.');
		}
		if (!is_dir($this->path) || !is_readable($this->path . 'ext_emconf.php')) {
			throw new \Exception('Cant read "' . $this->path . 'ext_emconf.php' . '"');
		}
	}

	/**
	 * @return array
	 */
	protected function getAccountData() {
		return array(
			'username' => $this->username,
			'password' => $this->password
		);
	}

	/**
	 * @return array
	 */
	protected function getExtensionData() {
		$emConf = $this->getEmConf();

		return array(
			'extensionKey' => utf8_encode($this->extensionKey),
			'version' => utf8_encode($emConf['version']),
			'metaData' => array(
				'title' => utf8_encode($emConf['title']),
				'description' => isset($emConf['description']) ? utf8_encode($emConf['description']) : '',
				'category' => isset($emConf['category']) ? utf8_encode($emConf['category']) : '',
				'state' => isset($emConf['state']) ? utf8_encode($emConf['state']) : '',
				'authorName' => isset($emConf['author']) ? utf8_encode($emConf['author']) : '',
				'authorEmail' => isset($emConf['author_email']) ? utf8_encode($emConf['author_email']) : '',
				'authorCompany' => isset($emConf['author_company']) ? utf8_encode($emConf['author_company']) : '',
			),
			'technicalData' => array(
				'dependencies' => $this->getDependenciesArray(),
				'loadOrder' => isset($emConf['loadOrder']) ? utf8_encode($emConf['loadOrder']) : '',
				'uploadFolder' => isset($emConf['uploadfolder']) ? (boolean) intval($emConf['uploadfolder']) : FALSE,
				'createDirs' => isset($emConf['createDirs']) ? utf8_encode($emConf['createDirs']) : '',
				'shy' => isset($emConf['shy']) ? (boolean) intval($emConf['shy']) : FALSE,
				'modules' => isset($emConf['module']) ? utf8_encode($emConf['module']) : '',
				'modifyTables' => isset($emConf['modify_tables']) ? utf8_encode($emConf['modify_tables']) : '',
				'priority' => isset($emConf['priority']) ? utf8_encode($emConf['priority']) : '',
				'clearCacheOnLoad' => isset($emConf['clearCacheOnLoad']) ? (boolean) intval($emConf['clearCacheOnLoad']) : FALSE,
				'lockType' => isset($emConf['lockType']) ? utf8_encode($emConf['lockType']) : '',
				'doNotLoadInFE' => isset($emConf['doNotLoadInFE']) ? utf8_encode($emConf['doNotLoadInFE']) : '',
				'docPath' => isset($emConf['docPath']) ? utf8_encode($emConf['docPath']) : '',
			),
			'infoData' => array(
				'codeLines' => 0,
				'codeBytes' => 0,
				'codingGuidelinesCompliance' => isset($emConf['CGLcompliance']) ? utf8_encode($emConf['CGLcompliance']) : '',
				'codingGuidelinesComplianceNotes' => isset($emConf['CGLcompliance_note']) ? utf8_encode($emConf['CGLcompliance_note']) : '',
				'uploadComment' => utf8_encode($this->uploadComment),
				'techInfo' => array(),
			),
		);
	}

	/**
	 * @return array
	 * @throws \Exception
	 */
	protected function getEmConf() {
		if ($this->emConf === NULL) {
			$this->emConf = Helper::getEmConf($this->extensionKey, $this->path);
		}
		return $this->emConf;
	}

	/**
	 * @return array
	 */
	protected function getDependenciesArray() {
		$emConf = $this->getEmConf();
		$dependenciesArr = array();

		if (isset($emConf['constraints']) && is_array($emConf['constraints'])) {
			$extKeysArr = $emConf['constraints']['depends'];

			if (is_array($extKeysArr)) {
				foreach ($extKeysArr as $extKey => $version) {
					if (strlen($extKey)) {
						$dependenciesArr[] = array(
							'kind' => 'depends',
							'extensionKey' => utf8_encode($extKey),
							'versionRange' => utf8_encode($version),
						);
					}
				}
			}

			$extKeysArr = $emConf['constraints']['conflicts'];
			if (is_array($extKeysArr)) {
				foreach ($extKeysArr as $extKey => $version) {
					if (strlen($extKey)) {
						$dependenciesArr[] = array(
							'kind' => 'conflicts',
							'extensionKey' => utf8_encode($extKey),
							'versionRange' => utf8_encode($version),
						);
					}
				}
			}
		}

		// FIXME: This part must be removed, when the problem is solved on the TER-Server #5919
		if (count($dependenciesArr) == 1) {
			$dependenciesArr[] = array(
				'kind' => 'depends',
				'extensionKey' => '',
				'versionRange' => '',
			);
		}
		// END for Bug #5919

		return $dependenciesArr;
	}

	/**
	 * @param $extensionKey
	 * @return TerUpload
	 */
	public function setExtensionKey($extensionKey) {
		$this->extensionKey = $extensionKey;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getExtensionKey() {
		return $this->extensionKey;
	}

	/**
	 * @param $password
	 * @return TerUpload
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param $path
	 * @return TerUpload
	 */
	public function setPath($path) {
		$this->path = rtrim($path, '/ ') . '/';
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * @param $uploadComment
	 * @return TerUpload
	 */
	public function setUploadComment($uploadComment) {
		$this->uploadComment = $uploadComment;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUploadComment() {
		return $this->uploadComment;
	}

	/**
	 * @param $username
	 * @return TerUpload
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @param $wsdlURL
	 * @return TerUpload
	 */
	public function setWsdlURL($wsdlURL) {
		$this->wsdlURL = $wsdlURL;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getWsdlURL() {
		return $this->wsdlURL;
	}

}