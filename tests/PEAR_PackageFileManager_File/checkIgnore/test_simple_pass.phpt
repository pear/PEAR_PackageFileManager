--TEST--
PEAR_PackageFileManager_File->checkIgnore, simple match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';

$pfm->_setupIgnore(array('frog*'), 1);
$pfm->_setupIgnore(array('frog*'), 0);

$res = $pfm->_checkIgnore('frog', 'anything\\goes\\frog', 1);
$phpunit->assertNotFalse($res, 'wrongo 1');

$res = $pfm->_checkIgnore('frog', 'anything\\goes\\frog', 0);
$phpunit->assertNotTrue($res, 'wrongo 2');

echo 'tests done';
?>
--EXPECT--
tests done