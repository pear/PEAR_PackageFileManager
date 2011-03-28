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
10

dir
309754
http://svn.php.net/repository/pear/packages/PEAR_PackageFileManager/trunk/tests/PEAR_PackageFileManager_Svn
http://svn.php.net/repository



2011-03-28T00:34:00.889028Z
309754
dufuz














c90b9560-bf6c-de11-be94-00142212c4b1
\x0c
test1.txt
file




2011-03-27T23:51:53.000000Z
80758d3dd42ffd2018c697a0bd782e35
2011-03-28T00:34:00.889028Z
309754
dufuz





















619
\x0c
test2.txt
file




2011-03-27T23:51:53.000000Z
80758d3dd42ffd2018c697a0bd782e35
2011-03-28T00:34:00.889028Z
309754
dufuz





















619
\x0c
.test
file




2011-03-27T23:51:53.000000Z
80758d3dd42ffd2018c697a0bd782e35
2011-03-28T00:34:00.889028Z
309754
dufuz





















619
\x0c
";
fwrite($z, $string);
fclose($z);

touch($file . 'test1.txt');
touch($file . 'test2.txt');
touch($file . '.test');