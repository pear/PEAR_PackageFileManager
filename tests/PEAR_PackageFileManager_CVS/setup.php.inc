<?php
error_reporting(E_ALL);
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'phpt_test.php.inc';
require_once 'PEAR/PackageFileManager.php';
require_once 'PEAR/PackageFileManager/Cvs.php';
$packagexml = new PEAR_PackageFileManager_Cvs($a, array('lang' => 'en'));
$phpunit = new PEAR_PHPTest(true);
?>