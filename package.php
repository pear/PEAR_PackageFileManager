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

$release_version = '1.0.0alpha1';
$release_state   = 'alpha';
$release_notes   = '
* Implemented Request #10945 Ignore should take directory into consideration [dufuz]
* Implemented Request #12820 Add glob functionality to PackageFileManager::addReplacement() patch provided by izi (David Jean Louis)
* Implemented Request #12932 .in files should have the src role [dufuz]
* Fixed Bug #13312 Please specify SimpleXML extension dependency [dufuz]
    XML_Serializer is now a required dep and simplexml is a optional one

Split from plugins and PFM1 for easier maintenance
';

$p = &PEAR_PackageFileManager2::importOptions(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package.xml',
    array(
      'packagefile' => 'package.xml',
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
    'PackageFileManager2.php',
    'examples/basicConvert.php',
    'examples/detectDependencies.php',
    'examples/easyMigration.php',
    'examples/makepackage.php',
    'examples/updatepackage.php',
    'tests/phpt_test.php.inc',
    'tests/setup.php.inc',
    'tests/PEAR_PackageFileManager2/',
));
$p->setPackageType('php');
$p->addRelease();
$p->clearDeps();
$p->setChannel('pear.php.net');
$p->setLicense('New BSD License', 'http://www.opensource.org/licenses/bsd-license.php');
$p->setReleaseVersion($release_version);
$p->setAPIVersion('1.0.0');
$p->setReleaseStability($release_state);
$p->setAPIStability('stable');
$p->setPhpDep('4.3.0');
$p->setPearinstallerDep('1.5.4');
$p->addPackageDepWithChannel('required', 'PEAR_PackageFileManager_Plugins', 'pear.php.net');
$p->addPackageDepWithChannel('optional', 'PHP_CompatInfo', 'pear.php.net', '1.4.0');
$p->addReplacement('PackageFileManager2.php', 'package-info', '@PEAR-VER@', 'version');
$p->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p->writePackageFile();
} else {
    $p->debugPackageFile();
}