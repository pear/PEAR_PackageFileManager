<?php
require_once 'PEAR/PackageFileManager.php';
$pf = new PEAR_PackageFileManager;
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$pf->setOptions(array(
'state' => 'stable',
'version' => '1.5.0',
'license' => 'PHP License',
'notes' => 'New features and bugfixes
* fix Bug #3696 PHP SAPI check in debugPackageFile() not reliable, use php_sapi_name() instead
* implement Request #3747 getOptions() method
* Migrate all unit tests to .phpt, run
  "pear run-tests -p PEAR_PackageFileManager" in PEAR 1.4.0 to run tests
  post-installation
* add dependency on XML_Tree (used by SVN driver)
* add package2.xml to the list of auto-ignored files',
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