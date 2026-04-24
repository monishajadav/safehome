<?php
$test_file = 'uploads/gallery/test.txt';
if(file_put_contents($test_file, 'Test')) {
    echo "✅ SUCCESS! Folder has write permissions.";
    unlink($test_file); // Delete test file
} else {
    echo "ERROR! Folder does not have write permissions.";
}
?>