--TEST--
PEAR_PackageFileManager->addDependency, valid, add php dep
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'File'));
$pfm->_packageXml['release_deps'] = array();
$pfm->addDependency('php', '4.3.0', 'has', 'php');
$phpunit->assertEquals(
    array('type' => 'php',
          'rel' => 'has', 'optional' => 'no'),
    $pfm->_packageXml['release_deps'][0],
    'release_deps value wrong');
echo 'tests done';
?>
--EXPECT--
tests done
