<?php
require_once 'PEAR/PackageFileManager.php';
$pf = new PEAR_PackageFileManager;
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$pf->setOptions(array(
'state' => 'alpha',
'version' => '1.5.0',
'license' => 'PHP License',
'notes' => 'New features and bugfixes
* Migrate all unit tests to .phpt
* add dependency on XML_Tree (used by SVN driver)',
'packagedirectory' => dirname(__FILE__),
'baseinstalldir' => 'PEAR',
'filelistgenerator' => 'CVS',
'simpleoutput' => true,
));
if (isset($_GET['make']) || isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make') {
    $pf->writePackageFile();
} else {
    $pf->debugPackageFile();
}
?>