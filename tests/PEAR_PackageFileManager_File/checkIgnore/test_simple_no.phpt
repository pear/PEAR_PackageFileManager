--TEST--
PEAR_PackageFileManager_File->checkIgnore, simple non-match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_setupIgnore(array('frog*'), 1);
$pfm->_setupIgnore(array('frog*'), 0);
$base = basename('anything\\goes');

$res = $pfm->_checkIgnore($base, 'anything\\goes', 1);
$phpunit->assertNotTrue($res, 'wrongo 1');

$res = $pfm->_checkIgnore($base, 'anything\\goes', 0);
$phpunit->assertNotFalse($res, 'wrongo 2');

echo 'tests done';
?>
--EXPECT--
tests done