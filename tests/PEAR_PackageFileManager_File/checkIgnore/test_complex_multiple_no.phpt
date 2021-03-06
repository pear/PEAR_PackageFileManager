--TEST--
PEAR_PackageFileManager_File->checkIgnore, multiple non-match
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_setupIgnore(array('gorf*', '*frog*/test.php'), 1);
$pfm->_setupIgnore(array('gorf*', '*frog*/test.php'), 0);
$base1 = basename('anything\\frooggoes\\test.php');
$base2 = basename('anything\\froggoes\\goorftest.php');


$res = $pfm->_checkIgnore($base1, 'anything\\frooggoes\\test.php', 1);
$phpunit->assertFalse($res, 'wrongo 1');
$res = $pfm->_checkIgnore($base2, 'anything\\frooggoes\\gorftest.php', 1);
$phpunit->assertFalse($res, 'wrongo 1.5');

$res = $pfm->_checkIgnore($base1, 'anything\\frooggoes\\test.php', 0);
$phpunit->assertTrue($res, 'wrongo 2');
$res = $pfm->_checkIgnore($base2, 'anything\\frooggoes\\gorftest.php', 0);
$phpunit->assertTrue($res, 'wrongo 2.5');

echo 'tests done';
?>
--EXPECT--
tests done