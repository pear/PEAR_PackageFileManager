--TEST--
PEAR_PackageFileManager_Cvs->dirList, valid test with include
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
mkdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS');
copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'testCVS'
    . DIRECTORY_SEPARATOR . 'testEntries',
    
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS' .
    DIRECTORY_SEPARATOR . 'Entries');
copy(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'testCVS'
    . DIRECTORY_SEPARATOR . 'testEntries.Extra',
    
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS' .
    DIRECTORY_SEPARATOR . 'Entries.Extra');
$packagexml->_options['addhiddenfiles'] = false;
$packagexml->_options['include'] = array('*1*');
$packagexml->_options['ignore'] = false;
$packagexml->_options['packagefile'] = 'package.xml';
$res = $packagexml->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest');
$phpunit->assertEquals(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/test1.txt',
    ),
    $res,
    'incorrect dir structure');
unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS' .
    DIRECTORY_SEPARATOR . 'Entries');
unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS' .
    DIRECTORY_SEPARATOR . 'Entries.Extra');
rmdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest' . DIRECTORY_SEPARATOR . 'CVS');
echo 'tests done';
?>
--EXPECT--
tests done