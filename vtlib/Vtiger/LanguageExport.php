<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
include_once 'vtlib/Vtiger/Package.php';

/**
 * Provides API to package vtiger CRM language files.
 * @package vtlib
 */
class Vtiger_LanguageExport extends Vtiger_Package {

	const TABLENAME = 'vtiger_language';

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Generate unique id for insertion
	 * @access public
	 */
	public static function __getUniqueId() {
		global $adb;
		return $adb->getUniqueID(self::TABLENAME);
	}

	/**
	 * Initialize Language Schema
	 * @access public
	 */
	public static function __initSchema() {
		$hastable = Vtiger_Utils::CheckTable(self::TABLENAME);
		if (!$hastable) {
			Vtiger_Utils::CreateTable(
				self::TABLENAME,
				'(id INT NOT NULL PRIMARY KEY,
				name VARCHAR(50), prefix VARCHAR(10), label VARCHAR(30), lastupdated DATETIME, sequence INT, isdefault INT(1), active INT(1))',
				true
			);
		}
	}

	/**
	 * Register language pack information.
	 */
	public static function register($prefix, $label, $name = '', $isdefault = false, $isactive = true, $overrideCore = false) {
		self::__initSchema();

		$prefix = trim($prefix);
		// We will not allow registering core language unless forced
		if (strtolower($prefix) == 'en_us' && !$overrideCore) {
			return;
		}

		$useisdefault = ($isdefault)? 1 : 0;
		$useisactive  = ($isactive)?  1 : 0;

		global $adb;
		$checkres = $adb->pquery('SELECT * FROM '.self::TABLENAME.' WHERE prefix=?', array($prefix));
		$datetime = date('Y-m-d H:i:s');
		if ($adb->num_rows($checkres)) {
			$id = $adb->query_result($checkres, 0, 'id');
			$adb->pquery(
				'UPDATE '.self::TABLENAME.' set label=?, name=?, lastupdated=?, isdefault=?, active=? WHERE id=?',
				array($label, $name, $datetime, $useisdefault, $useisactive, $id)
			);
		} else {
			$uniqueid = self::__getUniqueId();
			$adb->pquery(
				'INSERT INTO '.self::TABLENAME.' (id,name,prefix,label,lastupdated,isdefault,active) VALUES(?,?,?,?,?,?,?)',
				array($uniqueid, $name, $prefix, $label, $datetime, $useisdefault, $useisactive)
			);
		}
		self::log("Registering Language $label [$prefix] ... DONE");
	}

	/**
	 * De-Register language pack information
	 * @param string Language prefix like (de_de) etc
	 */
	public static function deregister($prefix) {
		$prefix = trim($prefix);
		// We will not allow deregistering core language
		if (strtolower($prefix) == 'en_us') {
			return;
		}

		self::__initSchema();

		global $adb;
		$adb->pquery('DELETE FROM '.self::TABLENAME.' WHERE prefix=?', array($prefix));
		self::log("Deregistering Language $prefix ... DONE");
	}

	/**
	 * Get all the language information
	 * @param boolean true to include in-active languages also, false (default)
	 */
	public static function getAll($includeInActive = false) {
		global $adb;
		$hastable = Vtiger_Utils::CheckTable(self::TABLENAME);

		$languageinfo = array();

		if ($hastable) {
			if ($includeInActive) {
				$result = $adb->pquery('SELECT * FROM '.self::TABLENAME, array());
			} else {
				$result = $adb->pquery('SELECT * FROM '.self::TABLENAME . ' WHERE active=?', array(1));
			}

			for ($index = 0; $index < $adb->num_rows($result); ++$index) {
				$resultrow = $adb->fetch_array($result);
				$prefix = $resultrow['prefix'];
				$label  = $resultrow['label'];
				$languageinfo[$prefix] = $label;
			}
		}
		return $languageinfo;
	}
}
?>
