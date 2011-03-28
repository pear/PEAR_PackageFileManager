--TEST--
PEAR_PackageFileManager_Svn, valid test 1
--SKIPIF--
--FILE--
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['addhiddenfiles'] = false;
$pfm->_options['ignore'] =
$pfm->_options['include'] = false;
$pfm->_options['packagefile'] = 'package.xml';


$z = fopen($file . '.svn' . DIRECTORY_SEPARATOR . 'entries', 'a');
fwrite($z, "\n/unused/1.16/dummy timestamp//");
fclose($z);

touch($file . '.svn' . DIRECTORY_SEPARATOR . 'unused');

$res = $pfm->dirList($file);
$phpunit->assertEquals(
    array(
        $file . 'test1.txt',
        $file . 'test2.txt',
        $file . '.test',
    ),
    $res,
    'incorrect dir structure');

unlink($file . '.svn' . DIRECTORY_SEPARATOR . 'unused');

echo 'tests done';
?>
--CLEAN--
<?php
require_once dirname(__FILE__) . '/teardown.php.inc';
?>
--EXPECT--
tests done