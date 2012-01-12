<?php

/*
 * Set include path so tests can be run from anywhere.
 */

if ('@php_dir@' == '@'.'php_dir'.'@') {
    // This package hasn't been installed, use parent of this dir.
    $php_dir = dirname(dirname(__FILE__));
} else {
    $php_dir = '@php_dir@';
}

$dirs = array(
    dirname($_SERVER['PHP_SELF']),
    dirname(dirname($_SERVER['PHP_SELF'])),
    dirname(__FILE__),
    $php_dir,
    '.',
);

$dirs = array_merge($dirs, explode(PATH_SEPARATOR, get_include_path()));
$dirs = array_unique($dirs);
set_include_path(implode(PATH_SEPARATOR, $dirs));


/*
 * Other tasks.
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'phpt_test.php.inc';
require_once 'PEAR/PackageFileManager.php';
$pfm = new PEAR_PackageFileManager;
$phpunit    = new PEAR_PHPTest(true);
