--TEST--
PEAR_PackageFileManager_File->dirList, bug #1217 test
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml->_options['addhiddenfiles'] = false;
$ignore = array('CVS/');
$packagexml->_setupIgnore(false, 0);
$packagexml->_setupIgnore($ignore, 1);
$res = $packagexml->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_bug1217');
$phpunit->assertEquals(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_bug1217/0',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_bug1217/firstfile.php',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_bug1217/fourthfile.php',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_bug1217/secondfile.php',
    ),
    $res,
    'incorrect dir structure');
echo 'tests done';
?>
--EXPECT--
tests done