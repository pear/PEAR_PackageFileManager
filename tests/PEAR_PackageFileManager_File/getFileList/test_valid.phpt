--TEST--
PEAR_PackageFileManager_File->dirList, valid listing, ignore option
--SKIPIF--
<?php
if (@file_exists(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'CVS')) {
    echo 'skip cannot run in CVS';
}
?>
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml->_options['addhiddenfiles'] = false;
$packagexml->_options['packagefile'] = 'package.xml';
$packagexml->_options['ignore'] =
$packagexml->_options['include'] = false;
$packagexml->_options['packagedirectory'] =
    dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/';
$res = $packagexml->getFileList();
$phpunit->assertEquals(
    array(
        '/' =>
        array(
        'blarfoo' =>
            array(
                array(
                    'file' => 'blartest.txt',
                    'ext' => 'txt',
                    'path' => 'blarfoo/blartest.txt',
                    'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/blarfoo/blartest.txt',
                )
            ),
        'subfoo' =>
            array(
                0 =>
                    array(
                        'file' => 'test11.txt',
                        'ext' => 'txt',
                        'path' => 'subfoo/test11.txt',
                        'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/test11.txt',
                    ),
                1 =>
                    array(
                        'file' => 'test12.txt',
                        'ext' => 'txt',
                        'path' => 'subfoo/test12.txt',
                        'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/test12.txt',
                    ),
                'subsubfoo' =>
                    array(
                        array(
                            'file' => 'boo.txt',
                            'ext' => 'txt',
                            'path' => 'subfoo/subsubfoo/boo.txt',
                            'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/subsubfoo/boo.txt',
                        )
                    ),
                ),
        'testCVS' =>
            array(
                0 =>
                    array(
                        'file' => 'testEntries',
                        'ext' => '',
                        'path' => 'testCVS/testEntries',
                        'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/testCVS/testEntries',
                    ),
                    array(
                        'file' => 'testEntries.Extra',
                        'ext' => 'Extra',
                        'path' => 'testCVS/testEntries.Extra',
                        'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/testCVS/testEntries.Extra',
                    ),
                ),
        0 =>
            array(
                'file' => 'test1.txt',
                'ext' => 'txt',
                'path' => 'test1.txt',
                'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/test1.txt',
            ),
        1 =>
            array(
                'file' => 'test2.txt',
                'ext' => 'txt',
                'path' => 'test2.txt',
                'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/test2.txt',
            ),
        )
    ),
    $res,
    'incorrect dir structure');
echo 'tests done';
?>
--EXPECT--
tests done