<?php
if (extension_loaded("zip")) {
    echo "ZIP extension is enabled";
} else {
    echo "ZIP extension is not enabled";
}
echo "\n\nLoaded extensions:\n";
print_r(get_loaded_extensions());
?>
