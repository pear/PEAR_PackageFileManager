<?php
require_once 'PEAR/PackageFileManager.php';
$pf = new PEAR_PackageFileManager;
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$pf->setOptions(array(
'state' => 'stable',
'version' => '1.5.1',
'license' => 'PHP License',
'notes' => 'Bugfix release
* fix Bug #4003 importOptions() won\'t work until setOptions() is called
',
'packagedirectory' => dirname(__FILE__),
'baseinstalldir' => 'PEAR',
'filelistgenerator' => 'CVS',
'simpleoutput' => true,
'ignore' => array('package.php'),
));
if (isset($_GET['make']) || isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make') {
    $pf->writePackageFile();
} else {
    $pf->debugPackageFile();
}
?>