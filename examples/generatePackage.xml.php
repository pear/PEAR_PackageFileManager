<?php
require_once('PEAR/PackageFileManager.php');
$test = new PEAR_PackageFileManager(
array('baseinstalldir' => 'PEAR',
'version' => '0.1',
'packagedirectory' => 'C:/cvsstuff/pear/pear_packagefilemanager',
'state' => 'alpha',
'filelistgenerator' => 'file',
'notes' => 'First release of PEAR_PackageFileManager',
'ignore' => array('package.xml')));
$test->addDependency('PEAR', '1.1');
$test->writePackageFile();
?>