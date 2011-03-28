--TEST--
PEAR_PackageFileManager_File->_setupIgnore, empty array
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_setupIgnore(array(), 1);
$phpunit->assertFalse($pfm->ignore[1], 'should be false if not an array');
echo 'tests done';
?>
--EXPECT--
tests done