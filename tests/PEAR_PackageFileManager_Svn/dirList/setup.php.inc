<?php
error_reporting(1803);
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'phpt_test.php.inc';
require_once 'PEAR/PackageFileManager/Svn.php';
$options    = array('lang' => 'en');
$pfm = new PEAR_PackageFileManager_Svn($options);
$phpunit    = new PEAR_PHPTest(true);

$file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
if (!file_exists($file)) {
    mkdir($file);
}

if (!file_exists($file . '.svn')) {
    mkdir($file . '.svn');
}

$z = fopen($file . '.svn/entries', 'w');
$string = "

";
fwrite($z, $string);
fclose($z);

touch($file . 'test1.txt');
touch($file . 'test2.txt');
touch($file . '.test');