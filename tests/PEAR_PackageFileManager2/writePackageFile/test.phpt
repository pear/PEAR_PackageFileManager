--TEST--
PEAR_PackageFileManager2->writePackageFile
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml = PEAR_PackageFileManager2::importOptions(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'packagefiles' .
    DIRECTORY_SEPARATOR . 'package1.xml', array('packagedirectory' => '.', 'baseinstalldir' => '/',
        'outputdirectory' => $temp_path));
$phpunit->assertNoErrors('setup');
$packagexml->setReleaseVersion('1.3.0a1');
$packagexml->setNotes('hi');
$packagexml->generateContents();
$packagexml->writePackageFile();
$phpunit->assertNoErrors('existing packagexml');
$phpunit->assertEquals('', '', 'contents');
echo 'tests done';
?>
--EXPECT--
tests done
