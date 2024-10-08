<?php
/*************************************************************************************************
 * Copyright 2022 JPL TSolucio, S.L. -- This file is a part of TSOLUCIO coreBOS Customizations.
* Licensed under the vtiger CRM Public License Version 1.1 (the "License"); you may not use this
* file except in compliance with the License. You can redistribute it and/or modify it
* under the terms of the License. JPL TSolucio, S.L. reserves all rights not expressly
* granted by the License. coreBOS distributed by JPL TSolucio S.L. is distributed in
* the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
* warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Unless required by
* applicable law or agreed to in writing, software distributed under the License is
* distributed on an "AS IS" BASIS, WITHOUT ANY WARRANTIES OR CONDITIONS OF ANY KIND,
* either express or implied. See the License for the specific language governing
* permissions and limitations under the License. You may obtain a copy of the License
* at <http://corebos.org/documentation/doku.php?id=en:devel:vpl11>
*************************************************************************************************/

class addDenormCRMEntityRel extends cbupdaterWorker {

	public function applyChange() {
		if ($this->hasError()) {
			$this->sendError();
		}
		if ($this->isApplied()) {
			$this->sendMsg('Changeset '.get_class($this).' already applied!');
		} else {
			$this->ExecuteQuery('CREATE TABLE IF NOT EXISTS `vtiger_crmentityreldenorm` (
				`crmid` int NOT NULL,
				`module` varchar(100) NOT NULL,
				`relcrmid` int NOT NULL,
				`relmodule` varchar(100) NOT NULL,
				INDEX (`crmid`),
				INDEX (`relcrmid`)
			) ENGINE=InnoDB');
			$this->ExecuteQuery('insert into vtiger_crmentityreldenorm (crmid,module,relcrmid,relmodule) (select crmid,module,relcrmid,relmodule from vtiger_crmentityrel)');
			$this->ExecuteQuery('insert into vtiger_crmentityreldenorm (crmid,module,relcrmid,relmodule) (select relcrmid,relmodule,crmid,module from vtiger_crmentityrel)');
			$this->sendMsg('Changeset '.get_class($this).' applied!');
			$this->markApplied();
		}
		$this->finishExecution();
	}
}