--TEST--
PEAR_PackageFileManager->_getExistingPackageXML, valid test, deps option set
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['pearcommonclass'] = 'PEAR_Common';
$pfm->_options['deps'] =
    array(
        array('name' => 'pork', 'rel' => 'ge', 'version' => '1.0.0',
              'optional' => 'yes')
    );
$res = $pfm->_getExistingPackageXML(dirname(dirname(__FILE__)) . '/', 'test1_package.xml');
$phpunit->assertFalse(is_object($res), 'returned error');

$info = dirname(dirname(__FILE__)) . '/test1_package.xml';
$packagefile = &new PEAR_PackageFile(PEAR_Config::singleton());
$pf = &$packagefile->fromAnyFile($info, PEAR_VALIDATE_NORMAL);
$contents = $pf->toArray();

$phpunit->assertEquals(array(
        array('name' => 'pork', 'rel' => 'ge', 'version' => '1.0.0',
              'optional' => 'yes')
    ), $pfm->_packageXml['release_deps'], 'wrong deps');
$phpunit->assertEquals($contents['maintainers'], $pfm->_packageXml['maintainers'], 'wrong maintainers');
$phpunit->assertEquals($pfm->_packageXml['release_deps'],
    $pfm->_options['deps'], 'wrong deps');
$phpunit->assertEquals($pfm->_packageXml['maintainers'],
    $pfm->_options['maintainers'], 'wrong maintainers');
echo 'tests done';
?>
--EXPECT--
tests done
