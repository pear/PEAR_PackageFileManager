--TEST--
PEAR_PackageFileManager_File->dirList, valid listing, addhiddenfiles option
--SKIPIF--
<?php
if (@file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'CVS')) {
    echo 'skip cannot run in CVS';
}
if (@file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . '.svn')) {
    echo 'skip cannot run in SVN';
}
?>
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['addhiddenfiles'] = true;
$pfm->_setupIgnore(false, 0);
$pfm->_setupIgnore(false, 1);
$res = $pfm->dirList(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest');
$phpunit->assertEquals(
    array(
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/.test',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/blarfoo/blartest.txt',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/subsubfoo/boo.txt',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/test11.txt',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/test12.txt',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/test1.txt',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/test2.txt',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/testCVS/testEntries',
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/testCVS/testEntries.Extra',
    ),
    $res,
    'incorrect dir structure');
echo 'tests done';
?>
--EXPECT--
tests done