--TEST--
PEAR_PackageFileManager->addDependency, valid, replace existing dep
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'File'));
$pfm->_packageXml['release_deps'] = array();
$pfm->addDependency('frog', '4.3.0', 'has', 'pkg', true);
$phpunit->assertEquals(
    array('name' => 'frog', 'type' => 'pkg',
          'rel' => 'has', 'optional' => 'yes'),
    $pfm->_packageXml['release_deps'][0],
    'release_deps value wrong');
$pfm->addDependency('frog', '4.3.0');
$phpunit->assertEquals(
    array('name' => 'frog', 'type' => 'pkg',
          'rel' => 'ge', 'version' => '4.3.0', 'optional' => 'no'),
    $pfm->_packageXml['release_deps'][0],
    'release_deps value wrong');
echo 'tests done';
?>
--EXPECT--
tests done
