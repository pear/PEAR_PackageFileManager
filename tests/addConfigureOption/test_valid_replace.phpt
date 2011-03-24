--TEST--
PEAR_PackageFileManager->addConfigureOption, update existing configure_option
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'File'));
$pfm->addConfigureOption('frog', 'Sound a frog makes', 'ribbit');
$phpunit->assertEquals(
    array(array('name' => 'frog', 'prompt' => 'Sound a frog makes', 'default' => 'ribbit')),
    $pfm->_packageXml['configure_options'],
    'configure_options value wrong');
$pfm->addConfigureOption('frog', 'Sound a frog makes');
$phpunit->assertEquals(
    array(array('name' => 'frog', 'prompt' => 'Sound a frog makes')),
    $pfm->_packageXml['configure_options'],
    'configure_options value wrong');
echo 'tests done';
?>
--EXPECT--
tests done
