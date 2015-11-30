<?php
/**
 * This is the package.xml generator for PEAR_PackageFileManager_Plugins
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
set_include_path(__DIR__ . PATH_SEPARATOR . get_include_path());
require_once 'PEAR/PackageFileManager2.php';
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$release_version = '1.0.3';
$release_state   = 'stable';
$release_notes   = '
* PHP 7 compliance
* Make unit tests portable, resilient, work
';

$p = &PEAR_PackageFileManager2::importOptions(
    dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package_plugins.xml',
    array(
        'packagefile' => 'package_plugins.xml',
        'exceptions' => array(
            'LICENSE_PLUGINS' => 'doc',
        ),
        'filelistgenerator' => 'git',
        'packagedirectory' => dirname(__FILE__),
        'changelogoldtonew' => false,
        'baseinstalldir' => 'PEAR',
        'simpleoutput' => true
    )
);
$p->setNotes($release_notes);
$p->addInclude(array(
    'LICENSE_PLUGINS',
    'PackageFileManager/Plugins.php',
    'PackageFileManager/File.php',
    'PackageFileManager/Svn.php',
    'PackageFileManager/Cvs.php',
    'PackageFileManager/Perforce.php',
    'tests/PEAR_PackageFileManager_File/',
    'tests/PEAR_PackageFileManager_CVS/',
    'tests/PEAR_PackageFileManager_Svn/',
    'tests/phpt_test.php.inc',
    'tests/setup.phpc.inc',
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
$p->addPackageDepWithChannel('required', 'XML_Serializer', 'pear.php.net', '0.19.0');
$p->addExtensionDep('optional', 'simplexml');

$p->addReplacement('PEAR/PackageFileManager/*.php', 'package-info', '@PEAR-VER@', 'version');
$p->addReplacement('tests/setup.php.inc', 'pear-config', '@php_dir@', 'php_dir');

$p->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
    $p->writePackageFile();
} else {
    $p->debugPackageFile();
}
