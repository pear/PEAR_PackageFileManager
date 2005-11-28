<?php
/**
 * This is the package.xml generator for PEAR_PackageFileManager
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
 * @copyright  2005 The PHP Group
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
      'baseinstalldir' => 'PEAR',
      'simpleoutput' => true));
$packagexml->setNotes('Bugfix release
* fix Bug #6037: Directories named "file" make problems');
$packagexml->addIgnore('*.tgz');
$packagexml->setPackageType('php');
$packagexml->addRelease();
$packagexml->setChannel('pear.php.net');
$packagexml->setReleaseVersion('1.6.0a5');
$packagexml->setAPIVersion('1.6.0');
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setPhpDep('4.2.0');
$packagexml->setPearinstallerDep('1.4.3');
$packagexml->addGlobalReplacement('package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $packagexml->writePackageFile();
} else {
    $packagexml->debugPackageFile();
}
?>