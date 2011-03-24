--TEST--
PEAR_PackageFileManager->_generateNewPackageXML, valid test, with deps
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['package'] = 'test';
$pfm->_options['summary'] = 'test';
$pfm->_options['description'] = 'test';
$pfm->_options['deps'] =
    array(
        array('name' => 'pork', 'rel' => 'ge', 'version' => '1.0.0',
              'optional' => 'yes')
    );
$ret = $pfm->_generateNewPackageXML();
$phpunit->assertFalse(is_object($ret), 'did not return true');
$phpunit->assertEquals(
    array (
  'package' => 'test',
  'summary' => 'test',
  'description' => 'test',
  'changelog' => 
  array (
  ),
  'release_deps' => 
  array (
    0 => 
    array (
      'name' => 'pork',
      'rel' => 'ge',
      'version' => '1.0.0',
      'optional' => 'yes',
    ),
  ),
  'maintainers' => 
  array (
  ),
),
    $pfm->_packageXml,
    'incorrect package');
echo 'tests done';
?>
--EXPECT--
tests done