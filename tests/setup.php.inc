<?php
error_reporting(1803);
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpt_test.php.inc';
require_once 'PEAR/PackageFileManager.php';
$pfm = new PEAR_PackageFileManager;
$phpunit    = new PEAR_PHPTest(true);