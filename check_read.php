<?php
$file = "modules/cbupdater/manifest.xml";
if (is_readable($file)) {
    echo "File is readable";
} else {
    echo "File is not readable";
}
$file2 = "test/vtlib/modules";
if (is_writable($file2)) {
    echo "\nUpload directory is writable";
} else {
    echo "\nUpload directory is not writable";
}
echo "\nCurrent user: " . get_current_user();
echo "\nPHP version: " . phpversion();
?>
