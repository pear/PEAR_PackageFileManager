--TEST--
PEAR_PackageFileManager_File->checkIgnore, simple directory, match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';

// 1 = ignore - 0 = include
$pfm->_setupIgnore(array('frog*/'), 1);
$pfm->_setupIgnore(array('frog*/'), 0);

$res = $pfm->_checkIgnore('test.php', 'anything\\froggoes\\test.php', 1);
$phpunit->assertNotFalse($res, 'wrongo 1');

$res = $pfm->_checkIgnore('test.php', 'anything\\froggoes\\test.php', 0);
$phpunit->assertNotTrue($res, 'wrongo 2');

echo 'tests done';
?>
--EXPECT--
tests done