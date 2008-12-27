--TEST--
PEAR_PackageFileManager_File->checkIgnore, simple match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';

$packagexml->_setupIgnore(array('frog*'), 1);
$packagexml->_setupIgnore(array('frog*'), 0);
$base = basename('anything\\goes\\frog');

$res = $packagexml->_checkIgnore($base, 'anything\\goes\\frog', 1);
$phpunit->assertNotFalse($res, 'wrongo 1');

$res = $packagexml->_checkIgnore($base, 'anything\\goes\\frog', 0);
$phpunit->assertNotTrue($res, 'wrongo 2');

echo 'tests done';
?>
--EXPECT--
tests done