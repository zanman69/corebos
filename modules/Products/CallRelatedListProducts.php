<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
if ($record != '') {
	$product_base_currency = getProductBaseCurrency($focus->id, $currentModule);
} else {
	$product_base_currency = fetchCurrency($current_user->id);
}
$smarty->assign('CURRENCY_ID', $product_base_currency);
$is_member = $focus->ismember_check();
$smarty->assign('IS_MEMBER', $is_member);
?>
