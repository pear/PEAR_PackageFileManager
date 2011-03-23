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

$release_version = '1.0.2';
$release_state   = 'stable';
$release_notes   = '
* Fixed Bug #16406: addReplacement does not look for files in packagedirectory [patch by Sune Jensen, dufuz]
* Fixed Bug #17451: Misleading error message about PHP_Compat/PHP_CompatInfo [dufuz]
';

$p = &PEAR_PackageFileManager2::importOptions(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package.xml',
    array(
      'packagefile' => 'package.xml',
      'exceptions' => array(
          'LICENSE'   => 'doc',
          'ChangeLog' => 'doc',
          'NEWS'      => 'doc'),
      'filelistgenerator' => 'svn',
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
$p->setPearinstallerDep('1.8.0');
$p->addPackageDepWithChannel('required', 'PEAR_PackageFileManager_Plugins', 'pear.php.net');
$p->addPackageDepWithChannel('optional', 'PHP_CompatInfo', 'pear.php.net', '1.4.0');
$p->addReplacement('PackageFileManager2.php', 'package-info', '@PEAR-VER@', 'version');
$p->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p->writePackageFile();
} else {
    $p->debugPackageFile();
}
