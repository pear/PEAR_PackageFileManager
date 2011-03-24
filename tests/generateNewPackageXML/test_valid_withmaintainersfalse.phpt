--TEST--
PEAR_PackageFileManager->_generateNewPackageXML, valid test, with maintainers set to false
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['package'] = 'test';
$pfm->_options['summary'] = 'test';
$pfm->_options['description'] = 'test';
$pfm->_options['maintainers'] = false;
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