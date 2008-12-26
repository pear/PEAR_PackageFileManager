--TEST--
PEAR_PackageFileManager_Cvs, valid test 2
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml->_options['addhiddenfiles'] = false;
//$packagexml->_options['ignore'] =
$packagexml->_options['include'] = false;
$packagexml->_options['packagefile'] = 'package.xml';

$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS' . DIRECTORY_SEPARATOR;
if (!file_exists($file)) {
  mkdir($file . 'CVS');
}

copy($file . 'testCVS' . DIRECTORY_SEPARATOR . 'testEntries',       $file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries');
copy($file . 'testCVS' . DIRECTORY_SEPARATOR . 'testEntries.Extra', $file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries.Extra');
touch($file . 'CVS' . DIRECTORY_SEPARATOR . 'unused');

$res = $packagexml->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest');
$phpunit->assertEquals(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/test1.txt',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/test2.txt',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/.test',
    ),
    $res,
    'incorrect dir structure');

unlink($file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries');
unlink($file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries.Extra');
unlink($file . 'CVS' . DIRECTORY_SEPARATOR . 'unused');
rmdir($file . 'CVS');

echo 'tests done';
?>
--EXPECT--
tests done