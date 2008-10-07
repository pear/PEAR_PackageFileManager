--TEST--
PEAR_PackageFileManager_Cvs->dirList, valid test with include
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml->_options['addhiddenfiles'] = false;
$packagexml->_options['include'] = array('*1*');
$packagexml->_options['ignore'] = false;
$packagexml->_options['packagefile'] = 'package.xml';

$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR;

mkdir($file . 'CVS');
copy($file . 'testCVS' . DIRECTORY_SEPARATOR . 'testEntries',      $file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries');
copy($file . 'testCVS'. DIRECTORY_SEPARATOR . 'testEntries.Extra', $file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries.Extra');

$res = $packagexml->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest');
$phpunit->assertEquals(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/test1.txt',
    ),
    $res,
    'incorrect dir structure');

unlink($file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries');
unlink($file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries.Extra');
rmdir($file . 'CVS');

echo 'tests done';
?>
--EXPECT--
tests done