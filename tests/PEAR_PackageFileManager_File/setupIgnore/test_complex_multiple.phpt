--TEST--
PEAR_PackageFileManager_File->_setupIgnore, complex test, multiple patterns
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';

$pfm->_setupIgnore(array('frog*', 'frog*/test.php'), 1);
$x = 'frog.*\\' . DIRECTORY_SEPARATOR . 'test\.php';
$phpunit->assertEquals(
    array('frog.*', array($x, 'test\.php')),
    $pfm->ignore[1], 'incorrect setup');

$pfm->_setupIgnore(array('frog*', 'frog*\\test.php'), 1);
$phpunit->assertEquals(
    array('frog.*', array($x, 'test\.php')),
    $pfm->ignore[1], 'incorrect setup');

echo 'tests done';
?>
--EXPECT--
tests done