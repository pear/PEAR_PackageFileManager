--TEST--
PEAR_PackageFileManager_File->checkIgnore, complex non-match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_setupIgnore(array('*frog*/test.php'), 1);
$pfm->_setupIgnore(array('*frog*/test.php'), 0);
$base = basename('anything\\frooggoes\\test.php');

$res = $pfm->_checkIgnore($base, 'anything\\frooggoes\\test.php', 1);
$phpunit->assertFalse($res, 'wrongo 1');

$res = $pfm->_checkIgnore($base, 'anything\\frooggoes\\test.php', 0);
$phpunit->assertTrue($res, 'wrongo 2');

echo 'tests done';
?>
--EXPECT--
tests done