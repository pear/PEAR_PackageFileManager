--TEST--
PEAR_PackageFileManager2->addIgnore, Bug #13747 - Ignore option excludes more than it should
--FILE--
<?php
require_once 'PEAR/PackageFileManager2.php';
$pfm = new PEAR_PackageFileManager2;

$pfm->setOptions(
    array(
        'baseinstalldir'    => '/',
        'filelistgenerator' => 'file',
        'packagedirectory'  => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bug13747',
        'packagefile'       => 'package.xml'
    )
);

$pfm->setPackage('Test');
$pfm->setSummary('Test');
$pfm->setDescription('Test');
$pfm->setUri('__uri');
$pfm->setLicense('New BSD');
$pfm->addMaintainer('lead', 'dufuz', 'Helgi', 'helgi@php.net');

$pfm->setPackageType('php');

$pfm->setAPIVersion('0.0.1');
$pfm->setReleaseVersion('0.0.1');
$pfm->setAPIStability('alpha');
$pfm->setReleaseStability('alpha');
$pfm->setNotes('release');
$pfm->addRelease();

$pfm->clearDeps();
$pfm->setPhpDep('5.2.0');
$pfm->setPearinstallerDep('1.8.1');

$pfm->addIgnore('conf/');

$pfm->generateContents();
$res = $pfm->debugPackageFile();
if (PEAR::isError($res)) {
     echo $res->toString()."\n";
}
?>
--EXPECTF--
Analyzing config/xml.php
Analyzing templates/confirm.php
Analyzing something.php
<?xml version="1.0" encoding="UTF-8"?>
<package packagerversion="1.10.0beta1" version="2.0" xmlns="http://pear.php.net/dtd/package-2.0" xmlns:tasks="http://pear.php.net/dtd/tasks-1.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://pear.php.net/dtd/tasks-1.0
    http://pear.php.net/dtd/tasks-1.0.xsd
    http://pear.php.net/dtd/package-2.0
    http://pear.php.net/dtd/package-2.0.xsd">
 <name>Test</name>
 <uri>__uri</uri>
 <summary>Test</summary>
 <description>Test</description>
 <lead>
  <name>Helgi</name>
  <user>dufuz</user>
  <email>helgi@php.net</email>
  <active>yes</active>
 </lead>
 <date>%s</date>
 <time>%s</time>
 <version>
  <release>0.0.1</release>
  <api>0.0.1</api>
 </version>
 <stability>
  <release>alpha</release>
  <api>alpha</api>
 </stability>
 <license>New BSD</license>
 <notes>
release
 </notes>
 <contents>
  <dir baseinstalldir="/" name="/">
   <file baseinstalldir="/" md5sum="d41d8cd98f00b204e9800998ecf8427e" name="config/xml.php" role="php" />
   <file baseinstalldir="/" md5sum="d41d8cd98f00b204e9800998ecf8427e" name="templates/confirm.php" role="php" />
   <file baseinstalldir="/" md5sum="d41d8cd98f00b204e9800998ecf8427e" name="something.php" role="php" />
  </dir>
 </contents>
 <dependencies>
  <required>
   <php>
    <min>5.2.0</min>
   </php>
   <pearinstaller>
    <min>1.8.1</min>
   </pearinstaller>
  </required>
 </dependencies>
 <phprelease />
 <changelog>
  <release>
   <version>
    <release>0.0.1</release>
    <api>0.0.1</api>
   </version>
   <stability>
    <release>alpha</release>
    <api>alpha</api>
   </stability>
   <date>%s</date>
   <license>New BSD</license>
   <notes>
release
   </notes>
  </release>
 </changelog>
</package>