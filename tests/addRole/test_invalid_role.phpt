--TEST--
PEAR_PackageFileManager->addRole, invalid
--SKIPIF--
--FILE--
<?php
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'setup.php.inc';
$packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
    'packagedirectory' => dirname(dirname(__FILE__)), 'baseinstalldir' => 'Foo',
    'packagefile' => 'test1_package.xml',
    'filelistgenerator' => 'File'));
$packagexml->addRole('frog', 'ribbit');
$phpunit->assertErrors(array(array('package' => 'PEAR_Error', 'message' =>
    'PEAR_PackageFileManager Error: Invalid file role passed to addRole, must be one of "' .
    implode(PEAR_Common::getFileRoles(), ', ') .
    '", was passed "ribbit"')),
    'invalid role test'
);
$phpunit->assertTrue(!isset($packagexml->_options['roles']['frog']),
    'extension was set, should not be');
echo 'tests done';
?>
--EXPECT--
tests done
