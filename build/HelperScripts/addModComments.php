<!DOCTYPE html>
<html><head><title>TSolucio::coreBOS Customizations</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel="stylesheet" type="text/css" media="all" href="themes/softed/style.css">
<style type="text/css">br { display: block; margin: 2px; }</style>
</head><body class=small style="font-size: 12px; margin: 2px; padding: 2px; background-color:#f7fff3; ">
<table width="100%" border=0><tr><td><span style='color:red;float:right;margin-right:30px;'><h2>Proud member of the <a href='http://corebos.org'>coreBOS</a> family!</h2></span></td></tr></table>
<hr style="height: 1px">
<?php
// Turn on debugging level
$Vtiger_Utils_Log = true;

include_once 'vtlib/Vtiger/Module.php';

if (empty($_REQUEST['modulename'])) {
	echo "<br/><H2>'modulename' parameter is mandatory</H2>";
	echo "<br/><H2>Es obligatorio introducir el parámetro 'modulename'</H2>";
	die();
}
$module = Vtiger_Module::getInstance($_REQUEST['modulename']);

if ($module) {
	include_once 'modules/ModComments/ModComments.php';
	if (class_exists('ModComments')) {
		ModComments::addWidgetTo(array($_REQUEST['modulename']));
		echo '<br/><H2>ModComments added to '.$_REQUEST['modulename'].' module.</H2><br>';
		echo '<br/><H2>ModComments añadido al módulo '.$_REQUEST['modulename'].'.</H2><br>';
	} else {
		echo '<br/><H2>Failed to find ModComments module.</h2><br>';
		echo '<br/><H2>No se ha podido encontrar el módulo ModComments.</h2><br>';
	}
} else {
	echo '<br/><H2>Failed to find '.$_REQUEST['modulename'].' module.</h2><br>';
	echo '<br/><H2>No se ha podido encontrar el módulo '.$_REQUEST['modulename'].'.</h2><br>';
}
?>
</body>
</html>
