--TEST--
PEAR_PackageFileManager_File->_setupDirs, valid test
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$pfm->_options['addhiddenfiles'] = false;
$pfm->_options['ignore'] =
$pfm->_options['include'] = false;
$pfm->_setupIgnore(false, 0);
$pfm->_setupIgnore(false, 1);
$list = $pfm->dirList($package_directory =
    dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest');
$struc = array();
foreach($list as $file) {
	$path = substr(dirname($file), strlen(str_replace(DIRECTORY_SEPARATOR, 
                                                      '/',
                                                      realpath($package_directory))) + 1);
	if (!$path) {
        $path = '/';
    }
    $a = explode('.', $file);
    $ext = array_pop($a);
	if (strlen($ext) == strlen($file)) {
        $ext = '';
    }
	$struc[$path][] = array('file' => basename($file),
                            'ext' => $ext,
                            'path' => (($path == '/') ? basename($file) : $path . '/' . basename($file)),
                            'fullpath' => $file);
}
uksort($struc,'strnatcasecmp');
foreach($struc as $key => $ind) {
	usort($ind, array($pfm, 'sortfiles'));
	$struc[$key] = $ind;
}
$test = $pfm->_setupDirs($struc['/'], explode('/','subfoo/subsubfoo'), $struc['subfoo/subsubfoo']);
$phpunit->assertEquals(
    array(
        0 =>
       array('file' => 'test1.txt',
              'ext' => 'txt',
              'path' => 'test1.txt',
              'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/test1.txt'),
        1 =>
        array('file' => 'test2.txt',
              'ext' => 'txt',
              'path' => 'test2.txt',
              'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/test2.txt'),
        'subfoo' =>
          array(
            'subsubfoo' =>
              array(
                array('file' => 'boo.txt',
                      'ext' => 'txt',
                      'path' => 'subfoo/subsubfoo/boo.txt',
                      'fullpath' => dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'footest/subfoo/subsubfoo/boo.txt')
                ),
              ),
    ),
    $test,
    'incorrect parsing'
);
echo 'tests done';
?>
--EXPECT--
tests done
