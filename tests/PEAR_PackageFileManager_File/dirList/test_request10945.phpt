--TEST--
PEAR_PackageFileManager_File->dirList, request #10945 test
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['packagedirectory'] = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_request10945/';
$pfm->_options['addhiddenfiles'] = false;
$ignore = array(
    'CVS/', 'upload/file.php',
);

$pfm->_setupIgnore(false, 0);
$pfm->_setupIgnore($ignore, 1);
$res = $pfm->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_request10945');
$phpunit->assertEquals(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_request10945/dir/file.php',
    ),
    $res,
    'incorrect dir structure');
echo 'tests done';
?>
--EXPECT--
tests done