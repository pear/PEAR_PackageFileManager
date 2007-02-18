<?php
/**
 * This is the package.xml generator for PEAR_PackageFileManager2
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   PEAR
 * @package    PEAR_PackageFileManager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      File available since Release 1.6.0
 */
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$release_version = '1.6.1';
$release_state   = 'stable';
$release_notes   = '
* fix Bug #9560: PPFM1 constants used in PPFM2
* fix Bug #9559: files not included under certain conditions
';

$packagexml = &PEAR_PackageFileManager2::importOptions(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package2.xml',
    array(
      'packagefile' => 'package2.xml',
      'exceptions' => array(
          'ChangeLog' => 'doc',
          'NEWS' => 'doc'),
      'filelistgenerator' => 'cvs',
      'packagedirectory' => dirname(__FILE__),
      'changelogoldtonew' => false,
      'baseinstalldir' => 'PEAR',
      'simpleoutput' => true
      ));
$packagexml->setNotes($release_notes);
$packagexml->addIgnore(array('package.php', '*.tgz'));
$packagexml->setPackageType('php');
$packagexml->updateMaintainer('lead', 'farell', 'Laurent Laville', 'farell@php.net', 'no');
$packagexml->addRelease();
$packagexml->clearDeps();
$packagexml->setChannel('pear.php.net');
$packagexml->setLicense('PHP License 3.01', 'http://www.php.net/license/3_01.txt');
$packagexml->setReleaseVersion($release_version);
$packagexml->setAPIVersion('1.6.0');
$packagexml->setReleaseStability($release_state);
$packagexml->setAPIStability('stable');
$packagexml->setPhpDep('4.2.0');
$packagexml->setPearinstallerDep('1.4.3');
$packagexml->addPackageDepWithChannel('optional', 'PHP_CompatInfo', 'pear.php.net', '1.4.0');
$packagexml->addReplacement('PackageFileManager.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager2.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/File.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/Cvs.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/Perforce.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/Svn.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/SimpleGenerator.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/XMLOutput.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/ComplexGenerator.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $packagexml->writePackageFile();
} else {
    $packagexml->debugPackageFile();
}
?>