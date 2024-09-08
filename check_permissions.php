<?php
function checkPermissions($path) {
    echo "Checking $path:\n";
    echo "Exists: " . (file_exists($path) ? 'Yes' : 'No') . "\n";
    echo "Readable: " . (is_readable($path) ? 'Yes' : 'No') . "\n";
    echo "Writable: " . (is_writable($path) ? 'Yes' : 'No') . "\n";
    echo "Permissions: " . substr(sprintf('%o', fileperms($path)), -4) . "\n";
    echo "Owner: " . posix_getpwuid(fileowner($path))['name'] . "\n";
    echo "Group: " . posix_getgrgid(filegroup($path))['name'] . "\n\n";
}

checkPermissions('modules/cbupdater/manifest.xml');
checkPermissions('test/vtlib/modules');
checkPermissions('.');  // Current directory

echo "Current working directory: " . getcwd() . "\n";
echo "Free disk space: " . disk_free_space("/") . " bytes\n";
?>
