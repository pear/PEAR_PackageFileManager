--TEST--
PEAR_PackageFileManager2->importOptions, bug #17990, date not set for all changelog releases
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm = PEAR_PackageFileManager2::importOptions(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'packagefiles' .
    DIRECTORY_SEPARATOR . 'package_bug17990.xml', array('packagedirectory' => '.', 'baseinstalldir' => '/'));

$phpunit->assertNoErrors('existing packagexml');
$phpunit->assertIsa('PEAR_PackageFileManager2', $pfm, 'packagefile');

$changelog = $pfm->writePackageFile();

echo 'tests done';
?>
--EXPECT--
tests done
