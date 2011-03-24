--TEST--
PEAR_PackageFileManager_File->checkIgnore, multiple match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';

$pfm->_setupIgnore(array('gorf*', '*frog*/test.php'), 1);
$pfm->_setupIgnore(array('gorf*', '*frog*/test.php'), 0);

$res = $pfm->_checkIgnore('test.php', 'anything\\froggoes\\test.php', 1);
$phpunit->assertNotFalse($res, 'wrongo 1');

$res = $pfm->_checkIgnore('gorftest.php', 'anything\\froggoes\\gorftest.php', 1);
$phpunit->assertNotFalse($res, 'wrongo 1.5');

$res = $pfm->_checkIgnore('test.php', 'anything\\froggoes\\test.php', 0);
$phpunit->assertNotTrue($res, 'wrongo 2');

$res = $pfm->_checkIgnore('gorftest.php', 'anything\\froggoes\\gorftest.php', 0);
$phpunit->assertNotTrue($res, 'wrongo 2.5');

echo 'tests done';
?>
--EXPECT--
tests done