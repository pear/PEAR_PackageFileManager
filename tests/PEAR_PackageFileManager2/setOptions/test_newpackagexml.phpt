--TEST--
PEAR_PackageFileManager2->setOptions, new package.xml
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$a = $pfm->setOptions(array('packagedirectory' => $temp_path, 'baseinstalldir' => '/'));
$phpunit->assertNoErrors('new packagexml');
$phpunit->assertNull($a, 'return');
$phpunit->assertIsa('PEAR_PackageFileManager2', $pfm->_oldPackageFile, 'old packagefile');
echo 'tests done';
?>
--EXPECT--
tests done
