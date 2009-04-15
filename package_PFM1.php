<?php
/**
 * This is the package.xml generator for PEAR_PackageFileManager2
 *
 * @category   PEAR
 * @package    PEAR_PackageFileManager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2005-2009 The PEAR Group
 * @license    New BSD, Revised
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      File available since Release 1.6.0
 */
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$release_version = '1.7.0';
$release_state   = 'stable';
$release_notes   = '
  No changes since alpha1
';

$p = &PEAR_PackageFileManager2::importOptions(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package_PFM1.xml',
    array(
      'packagefile' => 'package_PFM1.xml',
      'exceptions' => array(
          'LICENSE'   => 'doc',
          'ChangeLog' => 'doc',
          'NEWS'      => 'doc'),
      'filelistgenerator' => 'cvs',
      'packagedirectory' => dirname(__FILE__),
      'changelogoldtonew' => false,
      'baseinstalldir' => 'PEAR',
      'simpleoutput' => true
      ));
$p->setNotes($release_notes);
$p->addInclude(array(
    'LICENSE',
    'PackageFileManager.php',
    'PackageFileManager/ComplexGenerator.php',
    'PackageFileManager/SimpleGenerator.php',
    'PackageFileManager/XMLOutput.php',
    'examples/generatePackage.xml.php',
    'examples/createPhpDocumentor_package.xml.php',
    'tests/addConfigureOption/',
    'tests/addDependency/',
    'tests/addMaintainer/',
    'tests/addReplacement/',
    'tests/generateNewPackageXML/',
    'tests/getExistingPackageXML/',
    'tests/importOptions/',
    'tests/PEAR_PackageFileManager_XMLOutput/',
    'tests/setOptions/',
    'tests/Bad_file.php',
    'tests/phpt_test.php.inc',
    'tests/setup.php.inc',
    'tests/test1_package.xml',
    'tests/Test_file.php',
));
$p->setPackageType('php');
$p->addRelease();
$p->clearDeps();
$p->setChannel('pear.php.net');
$p->setLicense('New BSD License', 'http://www.opensource.org/licenses/bsd-license.php');
$p->setReleaseVersion($release_version);
$p->setAPIVersion('1.7.0');
$p->setReleaseStability($release_state);
$p->setAPIStability('stable');
$p->setPhpDep('4.3.0');
$p->setPearinstallerDep('1.8.0alpha1');
$p->addSubpackageDepWithChannel('required', 'PEAR_PackageFileManager_Plugins', 'pear.php.net');
$p->addSubpackageDepWithChannel('required', 'PEAR_PackageFileManager2', 'pear.php.net');
$p->addPackageDepWithChannel('optional', 'PHP_CompatInfo', 'pear.php.net', '1.4.0');
$p->addReplacement('PackageFileManager.php', 'package-info', '@PEAR-VER@', 'version');
$p->addReplacement('PackageFileManager/SimpleGenerator.php', 'package-info', '@PEAR-VER@', 'version');
$p->addReplacement('PackageFileManager/XMLOutput.php', 'package-info', '@PEAR-VER@', 'version');
$p->addReplacement('PackageFileManager/ComplexGenerator.php', 'package-info', '@PEAR-VER@', 'version');
$p->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p->writePackageFile();
} else {
    $p->debugPackageFile();
}
