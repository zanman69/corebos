<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
require_once 'modules/Import/models/Import_Map.php';
require_once 'modules/Import/resources/Utils.php';
require_once 'modules/Import/controllers/Import_Data_Controller.php';
require_once 'modules/Import/ui/Viewer.php';

class Import_Controller {

	public $userInputObject;
	public $user;
	public $numberOfRecords;

	public function __construct($userInputObject, $user) {
		$this->userInputObject = $userInputObject;
		$this->user = $user;
	}

	public static function import($userInputObject, $user) {
		$importController = new Import_Controller($userInputObject, $user);

		$importController->saveMap();
		$fileReadStatus = $importController->copyFromFileToDB();
		if ($fileReadStatus) {
			$importController->queueDataImport();
		}

		$isImportScheduled = $importController->userInputObject->get('is_scheduled');

		if ($isImportScheduled) {
			$importInfo = Import_Queue_Controller::getUserCurrentImportInfo($importController->user);
			self::showScheduledStatus($importInfo);
		} else {
			$importController->triggerImport();
		}
	}

	public function triggerImport($batchImport = false, $setBatchImport = false) {
		$importInfo = Import_Queue_Controller::getImportInfo($this->userInputObject->get('module'), $this->user);
		$importInfo['workflow'] = $this->userInputObject->get('workflow');
		if ($importInfo['workflow'] == 'on') {
			$importInfo['workflow'] = 1;
		}
		if ($importInfo['workflow'] == 'off') {
			$importInfo['workflow'] = 0;
		}
		$importInfo['workflowid'] = $this->userInputObject->get('workflowid');
		$importDataController = new Import_Data_Controller($importInfo, $this->user);

		if (!$batchImport && !$importDataController->initializeImport()) {
			Import_Utils::showErrorPage(getTranslatedString('ERR_FAILED_TO_LOCK_MODULE', 'Import'));
			exit;
		}
		if ($setBatchImport) {
			$importDataController->batchImport = false;
		}
		Import_Queue_Controller::updateStatus($importInfo['id'], Import_Queue_Controller::$IMPORT_STATUS_RUNNING);
		$importDataController->importData();
		Import_Queue_Controller::updateStatus($importInfo['id'], Import_Queue_Controller::$IMPORT_STATUS_HALTED);
		$importInfo = Import_Queue_Controller::getImportInfo($this->userInputObject->get('module'), $this->user);

		self::showImportStatus($importInfo, $this->user);
	}

	public static function showImportStatus($importInfo, $user) {
		if ($importInfo == null) {
			Import_Utils::showErrorPage(getTranslatedString('ERR_IMPORT_INTERRUPTED', 'Import'));
			exit;
		}
		$importDataController = new Import_Data_Controller($importInfo, $user);
		if ($importInfo['status'] == Import_Queue_Controller::$IMPORT_STATUS_HALTED ||
				$importInfo['status'] == Import_Queue_Controller::$IMPORT_STATUS_NONE) {
			$continueImport = true;
		} else {
			$continueImport = false;
		}

		$importStatusCount = $importDataController->getImportStatusCount();
		$totalRecords = $importStatusCount['TOTAL'];
		$ImportNotCheckApache = !filter_var(GlobalVariable::getVariable('Import_Check_Apache', 1, $importInfo['module'], $user->id), FILTER_VALIDATE_BOOLEAN);
		if ($totalRecords > ($importStatusCount['IMPORTED'] + $importStatusCount['FAILED']) && ($ImportNotCheckApache || strpos(PHP_SAPI, 'apache')!==false)) {
			self::showCurrentStatus($importInfo, $importStatusCount, $continueImport);
			exit;
		} else {
			$importDataController->finishImport();
			self::showResult($importInfo, $importStatusCount);
		}
	}

	public static function showCurrentStatus($importInfo, $importStatusCount, $continueImport) {
		$moduleName = $importInfo['module'];
		$importId = $importInfo['id'];
		$viewer = new Import_UI_Viewer();
		$viewer->assign('FOR_MODULE', $moduleName);
		$viewer->assign('IMPORT_ID', $importId);
		$viewer->assign('IMPORT_RESULT', $importStatusCount);
		$viewer->assign('CONTINUE_IMPORT', $continueImport);
		$viewer->display('ImportStatus.tpl');
	}

	public static function showResult($importInfo, $importStatusCount) {
		$moduleName = $importInfo['module'];
		$ownerId = $importInfo['user_id'];
		$viewer = new Import_UI_Viewer();
		$viewer->assign('FOR_MODULE', $moduleName);
		$viewer->assign('OWNER_ID', $ownerId);
		$viewer->assign('IMPORT_RESULT', $importStatusCount);
		$viewer->assign('MERGE_ENABLED', $importInfo['merge_type']);
		if (PHP_SAPI == 'cli') {
			$viewer->display('ImportResultCLI.tpl');
		} else {
			$viewer->display('ImportResult.tpl');
		}
	}

	public static function showScheduledStatus($importInfo) {
		$moduleName = $importInfo['module'];
		$importId = $importInfo['id'];
		$viewer = new Import_UI_Viewer();
		$viewer->assign('FOR_MODULE', $moduleName);
		$viewer->assign('IMPORT_ID', $importId);
		$viewer->display('ImportSchedule.tpl');
	}

	public function saveMap() {
		$saveMap = $this->userInputObject->get('save_map');
		$mapName = $this->userInputObject->get('save_map_as');
		if ($saveMap && !empty($mapName)) {
			$fieldMapping = $this->userInputObject->get('field_mapping');
			$defaultValues = $this->userInputObject->get('default_values');
			$fileReader = Import_Utils::getFileReader($this->userInputObject, $this->user);
			if ($fileReader == null) {
				return false;
			}
			$hasHeader = $fileReader->hasHeader();
			if ($hasHeader) {
				$firstRowData = $fileReader->getFirstRowData($hasHeader);
				$headers = array_keys($firstRowData);
				foreach ($fieldMapping as $fieldName => $index) {
					$saveMapping[$headers[$index]] = $fieldName;
				}
			} else {
				$saveMapping = array_flip($fieldMapping);
			}
			$saveDefaultValue = array();
			foreach ($defaultValues as $field => $value) {
				$saveDefaultValue[$field] = $value;
			}

			$map = array();
			$map['name'] = $mapName;
			$map['content'] = $saveMapping;
			$map['module'] = $this->userInputObject->get('module');
			$map['has_header'] = ($hasHeader)?1:0;
			$map['assigned_user_id'] = $this->user->id;
			$map['defaultvalues'] = json_encode($saveDefaultValue);
			$map['field_mapping'] = json_encode($this->userInputObject->get('field_mapping'));

			$importMap = new Import_Map($map, $this->user);
			$importMap->save();
		}
	}

	public function copyFromFileToDB() {
		$fileReader = Import_Utils::getFileReader($this->userInputObject, $this->user);
		$fileReader->read();
		$fileReader->deleteFile();
		if ($fileReader->getStatus() == 'success') {
			$this->numberOfRecords = $fileReader->getNumberOfRecordsRead();
			return true;
		} else {
			Import_Utils::showErrorPage(getTranslatedString('ERR_FILE_READ_FAILED', 'Import').' - '.getTranslatedString($fileReader->getErrorMessage(), 'Import'));
			return false;
		}
	}

	public function queueDataImport($forceSchedule = false) {
		$configReader = new ConfigReader('modules/Import/config.inc', 'ImportConfig');
		$immediateImportRecordLimit = $configReader->getConfig('immediateImportLimit');
		$numberOfRecordsToImport = $this->numberOfRecords;
		if ($forceSchedule || $numberOfRecordsToImport > $immediateImportRecordLimit) {
			$this->userInputObject->set('is_scheduled', true);
		}
		Import_Queue_Controller::add($this->userInputObject, $this->user);
	}
}

?>