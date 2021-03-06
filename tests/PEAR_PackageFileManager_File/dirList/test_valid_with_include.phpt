--TEST--
PEAR_PackageFileManager_File->dirList, valid listing, include option
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['addhiddenfiles'] = false;
$pfm->_setupIgnore(array('blar*'), 0);
$pfm->_setupIgnore(false, 1);
$res = $pfm->dirList(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest');
$phpunit->assertEquals(
    array(
        dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/blarfoo/blartest.txt',
    ),
    $res,
    'incorrect dir structure');
echo 'tests done';
?>
--EXPECT--
tests done