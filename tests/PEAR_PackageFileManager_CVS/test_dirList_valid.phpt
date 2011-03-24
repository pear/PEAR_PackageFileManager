--TEST--
PEAR_PackageFileManager_Cvs->dirList, valid test
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['addhiddenfiles'] = false;
$pfm->_options['ignore'] = $pfm->_options['include'] = false;
$pfm->_options['packagefile'] = 'package.xml';


$res = $pfm->dirList(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp');
$phpunit->assertNoErrors('after dirlist');
$phpunit->assertEquals(
    array(
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp/test1.txt',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp/test2.txt',
        dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp/.test',
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
