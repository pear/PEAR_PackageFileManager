--TEST--
PEAR_PackageFileManager->addRole, valid
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'File'));
$pfm->addRole('frog', 'php');
$phpunit->assertEquals('php', $pfm->_options['roles']['frog'],
    'extension was not set, should be');
echo 'tests done';
?>
--EXPECT--
tests done
