<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Import_Map {

	public static $tableName = 'vtiger_import_maps';
	public $map;
	public $user;

	public function __construct($__map, $user) {
		$this->map = $__map;
		$this->user = $user;
	}

	public static function getInstanceFromDb($row, $user) {
		global $default_charset;
		$__map = array();
		foreach ($row as $key => $value) {
			if (is_numeric($key)) {
				continue;
			}
			if ($key == 'content') {
				$content = array();
				$pairs = explode('&', html_entity_decode($value, ENT_QUOTES, $default_charset));
				foreach ($pairs as $pair) {
					list($mappedName, $sequence) = explode('=', $pair);
					$mappedName = str_replace('/eq/', '=', $mappedName);
					$mappedName = str_replace('/amp/', '&', $mappedName);
					$content[$mappedName] = $sequence;
				}
				$__map[$key] = $content;
			} else {
				$__map[$key] = $value;
			}
		}
		return new Import_Map($__map, $user);
	}

	public static function markAsDeleted($mapId) {
		$adb = PearDatabase::getInstance();
		$adb->pquery('UPDATE vtiger_import_maps SET deleted=1 WHERE id=?', array($mapId));
	}

	public function getId() {
		return $this->map['id'];
	}

	public function getAllValues() {
		return $this->map;
	}

	public function getValue($key) {
		return $this->map[$key];
	}

	public function getStringifiedContent() {
		if (empty($this->map['content'])) {
			return '';
		}
		$content = $this->map['content'];
		$keyValueStrings = array();
		foreach ($content as $key => $value) {
			$key = str_replace('=', '/eq/', $key);
			$key = str_replace('&', '/amp/', $key);
			$keyValueStrings[] = $key.'='.$value;
		}
		return implode('&', $keyValueStrings);
	}

	public function getDefaultValues() {
		if (empty($this->map['defaultvalues'])) {
			return '';
		}
		return $this->map['defaultvalues'];
	}

	public function save() {
		$adb = PearDatabase::getInstance();

		$__map = $this->getAllValues();
		$__map['content'] = ''.$adb->getEmptyBlob().'';
		$columnNames = array_keys($__map);
		$columnValues = array_values($__map);
		if (!empty($__map)) {
			$adb->pquery(
				'INSERT INTO '.self::$tableName.' ('. implode(',', $columnNames).',date_entered) VALUES ('. generateQuestionMarks($columnValues).',now())',
				array($columnValues)
			);
			$adb->updateBlob(
				self::$tableName,
				'content',
				"name='". $adb->sql_escape_string($this->getValue('name')). "' AND module='".$adb->sql_escape_string($this->getValue('module'))."'",
				$this->getStringifiedContent()
			);
		}
	}

	public static function getAllByModule($moduleName) {
		global $current_user;
		$adb = PearDatabase::getInstance();

		$result = $adb->pquery('SELECT * FROM '.self::$tableName.' WHERE deleted=0 AND module=?', array($moduleName));
		$noOfMaps = $adb->num_rows($result);

		$savedMaps = array();
		for ($i=0; $i<$noOfMaps; ++$i) {
			$importMap = Import_Map::getInstanceFromDb($adb->query_result_rowdata($result, $i), $current_user);
			$savedMaps[$importMap->getId()] = $importMap;
		}
		return $savedMaps;
	}
}
?>
