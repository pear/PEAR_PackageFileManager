<?php
//require_once 'PEAR/PackageFileManager.php';
//$pf = new PEAR_PackageFileManager;
//PEAR::setErrorHandling(PEAR_ERROR_DIE);
//$pf->setOptions(array(
//'state' => 'stable',
//'version' => '1.5.3',
//'license' => 'PHP License',
//'notes' => 'Bugfix release
//* fix notice if simpleoutput is used, and there are no subdirectories
//',
//'packagedirectory' => dirname(__FILE__),
//'baseinstalldir' => 'PEAR',
//'filelistgenerator' => 'CVS',
//'simpleoutput' => true,
//'ignore' => array('package.php'),
//));
//if (isset($_GET['make']) || isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make') {
//    $pf->writePackageFile();
//} else {
//    $pf->debugPackageFile();
//}
?>
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
* fix many bugs in PackageFileManager2, particularly in relation
  to package.xml 1.0 import and export, replacements
* fix bug #4478: Notice error on File.php (generator)
* fix Bug #4525: Update inline package generation example
* fix Bug #4743: PHP 5.1 pass-by-reference error');
$packagexml->addIgnore('*.tgz');
$packagexml->setPackageType('php');
$packagexml->addRelease();
$packagexml->setChannel('pear.php.net');
$packagexml->setReleaseVersion('1.6.0a2');
$packagexml->setAPIVersion('1.6.0');
$packagexml->setReleaseStability('alpha');
$packagexml->setAPIStability('alpha');
$packagexml->setPhpDep('4.2.0');
$packagexml->setPearinstallerDep('1.4.0a12');
$packagexml->addGlobalReplacement('package-info', '@PEAR-VER@', 'version');
$packagexml->generateContents();
if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $packagexml->writePackageFile();
} else {
    $packagexml->debugPackageFile();
}
?>