--TEST--
PEAR_PackageFileManager_Svn->dirList, valid test with include
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['addhiddenfiles'] = false;
$pfm->_options['include'] = array('*1*');
$pfm->_options['ignore'] = false;
$pfm->_options['packagefile'] = 'package.xml';

$res = $pfm->dirList($file);
$phpunit->assertEquals(
    array(
        $file . 'test1.txt',
    ),
    $res,
    'incorrect dir structure');

echo 'tests done';
?>
--CLEAN--
<?php
require_once dirname(__FILE__) . '/teardown.php.inc';
?>
--EXPECT--
tests done