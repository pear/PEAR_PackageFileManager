--TEST--
PEAR_PackageFileManager->setOptions, valid, custom filelist generator
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'Test_file',
    'usergeneratordir' => dirname(dirname(__FILE__))
    ));
$phpunit->assertNoErrors('test');
echo 'tests done';
?>
--EXPECT--
tests done
