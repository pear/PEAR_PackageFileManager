<?php
/**
 * A simple example
 * @package PEAR_PackageFileManager
 */
/**
 * Include the package file manager
 */
require_once('PEAR/PackageFileManager.php');
$test = new PEAR_PackageFileManager;
if (PEAR::isError($test)) {
    echo $test->getMessage();
    exit;
}
$test->setOptions(
array('baseinstalldir' => 'PEAR',
'version' => '0.1',
'packagedirectory' => 'C:/cvsstuff/pear/pear_packagefilemanager',
'state' => 'alpha',
'filelistgenerator' => 'file',
'notes' => 'First release of PEAR_PackageFileManager',
'ignore' => array('package.xml')));
$test->addDependency('PEAR', '1.1');
$e = $test->writePackageFile();
if (PEAR::isError($e)) {
    echo $e->getMessage();
}
?>