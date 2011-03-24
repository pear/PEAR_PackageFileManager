--TEST--
PEAR_PackageFileManager_File->_setupIgnore, simple test, 0 index
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_setupIgnore(array('frog*'), 0);
$phpunit->assertEquals(
    array('frog.*'),
    $pfm->ignore[0], 'incorrect setup');
echo 'tests done';
?>
--EXPECT--
tests done