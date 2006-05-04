<?php
/**
 * This is the package.xml generator for PEAR_PackageFileManager2
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR_PackageFileManager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      File available since Release 1.6.0
 */
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);
$packagexml = &PEAR_PackageFileManager2::importOptions(dirname(__FILE__) . DIRECTORY_SEPARATOR .
    'package.xml', array(
      'filelistgenerator' => 'cvs',
      'packagedirectory' => dirname(__FILE__),
      'changelogoldtonew' => false,
      'baseinstalldir' => 'PEAR',
      'simpleoutput' => true));
$packagexml->setNotes('* bugs fixed
- #6843 : don\'t rely on XML/Tree (PEAR_PackageFileManager_Svn)
- #6357 : Notice Errors with PFM2 1.6.0a4 and CVS plugin
- #7393 : addGlobalReplacement() error reporting throws error
- #7496 : simpleoutput ignores installexceptions option

* quality assurance
- updated headers comment block ( http://pear.php.net/pepr/pepr-proposal-show.php?id=128 )
');
$packagexml->addIgnore(array('package.php','*.tgz'));
$packagexml->setPackageType('php');
//$packagexml->addMaintainer('lead', 'farell', 'Laurent Laville', 'pear@laurent-laville.org');
$packagexml->addRelease();
$packagexml->clearDeps();
$packagexml->setChannel('pear.php.net');
$packagexml->setReleaseVersion('1.6.0b1');
$packagexml->setAPIVersion('1.6.0');
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setPhpDep('4.2.0');
$packagexml->setPearinstallerDep('1.4.3');
$packagexml->addReplacement('PackageFileManager2.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/File.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/Cvs.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/Perforce.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/Svn.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/SimpleGenerator.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->addReplacement('PackageFileManager/XMLOutput.php', 'package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $packagexml->writePackageFile();
} else {
    $packagexml->debugPackageFile();
}
?>