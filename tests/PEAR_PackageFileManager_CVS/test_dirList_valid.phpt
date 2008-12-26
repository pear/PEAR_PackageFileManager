--TEST--
PEAR_PackageFileManager_Cvs->dirList, valid test
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml->_options['addhiddenfiles'] = false;
$packagexml->_options['ignore'] = $packagexml->_options['include'] = false;
$packagexml->_options['packagefile'] = 'package.xml';

$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR;
if (!file_exists($file)) {
    mkdir($file . 'CVS');
}

copy($file . 'testCVS' . DIRECTORY_SEPARATOR . 'testEntries',       $file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries');
copy($file . 'testCVS' . DIRECTORY_SEPARATOR . 'testEntries.Extra', $file . 'CVS' . DIRECTORY_SEPARATOR . 'Entries.Extra');


$res = $packagexml->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest');
$phpunit->assertNoErrors('after dirlist');
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
rmdir($file . 'CVS');

echo 'tests done';
?>
--EXPECT--
tests done
