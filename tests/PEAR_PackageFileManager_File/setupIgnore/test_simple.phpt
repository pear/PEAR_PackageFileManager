--TEST--
PEAR_PackageFileManager_File->_setupIgnore, simple test
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_setupIgnore(array('frog*'), 1);
$phpunit->assertEquals(
    array('frog.*'),
    $pfm->ignore[1], 'incorrect setup');
echo 'tests done';
?>
--EXPECT--
tests done