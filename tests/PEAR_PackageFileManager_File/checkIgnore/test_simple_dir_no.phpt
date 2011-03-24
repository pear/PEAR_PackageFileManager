--TEST--
PEAR_PackageFileManager_File->checkIgnore, simple non-match with directory
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';

// 1 = ignore - 0 = include
$pfm->_setupIgnore(array('frog*/'), 1);
$pfm->_setupIgnore(array('frog*/'), 0);

$res = $pfm->_checkIgnore('test.php', 'anything\\frooggoes\\test.php', 1);
$phpunit->assertFalse($res, 'wrongo 1');

$res = $pfm->_checkIgnore('test.php', 'anything\\frooggoes\\test.php', 0);
$phpunit->assertTrue($res, 'wrongo 2');

echo 'tests done';
?>
--EXPECT--
tests done