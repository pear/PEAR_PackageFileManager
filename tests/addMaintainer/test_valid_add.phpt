--TEST--
PEAR_PackageFileManager->addMaintainer, add new maintainer
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'File'));
$pfm->_packageXml['maintainers'] = array();
$pfm->addMaintainer('frog', 'lead', 'tadpole meister', 'frog@example.com');
$phpunit->assertEquals(
    array('handle' => 'frog', 'role' => 'lead',
             'name' => 'tadpole meister', 'email' => 'frog@example.com'),
    $pfm->_packageXml['maintainers'][0],
    'maintainers value wrong');
echo 'tests done';
?>
--EXPECT--
tests done
