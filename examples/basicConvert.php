<?php
/**
 * Basic convert package.xml 1.0 to package.xml 2.0
 *
 * @version    $Id$
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @package    PEAR_PackageFileManager
 * @ignore
 */

require_once 'PEAR/PackageFileManager2.php';

//PEAR::setErrorHandling(PEAR_ERROR_DIE);

// Configuration of PEAR::PackageFileManager
$optionsUpdate = array(
    'cleardependencies' => false,
    'clearcontents'     => false,
    'baseinstalldir'    => 'HTML',
    'simpleoutput'      => true,
    'filelistgenerator' => 'cvs',
    'changelogoldtonew' => false,
    'packagefile'       => 'package2.xml'
);

// Location of your package.xml 1.0 version
$packagefile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'package_DB-1.7.6.xml';
$packagefile = 'D:\php\pear\HTML_Progress2\package.xml';

$packagefileExists = file_exists($packagefile);

if ($packagefileExists) {
    $pkg = &PEAR_PackageFileManager2::importFromPackageFile1($packagefile, $optionsUpdate);
} else {
    die('Your package.xml 1.0 does not exists.');
}

$pkg->generateContents();

// Writes the new version of package.xml

if (isset($_GET['make']) || isset($_SERVER['argv'][1]) && $_SERVER['argv'][1] == 'make') {
    $pkg->writePackageFile();
} else {
    $pkg->debugPackageFile();
}

?>