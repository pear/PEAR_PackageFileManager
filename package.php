<?php
/**
 * This is the package.xml generator for PEAR_PackageFileManager2
 *
 * @category   PEAR
 * @package    PEAR_PackageFileManager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2003-2015 The PEAR Group
 * @license    New BSD, Revised
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      File available since Release 1.6.0
 */
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$release_version = '1.0.4';
$release_state   = 'stable';
$release_notes   = '
* Correct instatllation directory
';

$p = &PEAR_PackageFileManager2::importOptions(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package.xml',
    array(
      'packagefile' => 'package.xml',
      'exceptions' => array(
          'LICENSE'   => 'doc',
          'ChangeLog' => 'doc',
          'NEWS'      => 'doc'),
      'filelistgenerator' => 'git',
      'packagedirectory' => dirname(__FILE__),
      'changelogoldtonew' => false,
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
$p->setPhpDep('5.3.0');
$p->setPearinstallerDep('1.10.1');
$p->addPackageDepWithChannel('required', 'PEAR_PackageFileManager_Plugins', 'pear.php.net');
$p->addPackageDepWithChannel('optional', 'PHP_CompatInfo', 'pear.php.net', '1.4.0');
$p->addReplacement('PEAR/PackageFileManager2.php', 'package-info', '@PEAR-VER@', 'version');
$p->addReplacement('tests/setup.php.inc', 'pear-config', '@php_dir@', 'php_dir');
$p->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p->writePackageFile();
} else {
    $p->debugPackageFile();
}
