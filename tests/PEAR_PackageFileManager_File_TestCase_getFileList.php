<?php

/**
 * API Unit tests for PEAR_PackageFileManager package.
 * 
 * @version    $Id$
 * @author     Laurent Laville <pear@laurent-laville.org> portions from HTML_CSS
 * @author     Greg Beaver
 * @package    PEAR_PackageFileManager
 */

/**
 * @package PEAR_PackageFileManager
 */

class PEAR_PackageFileManager_File_TestCase_getFileList extends PHPUnit_TestCase
{
    /**
     * A PEAR_PackageFileManager object
     * @var        object
     */
    var $packagexml;

    function PEAR_PackageFileManager_File_TestCase_getFileList($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL);
        $this->errorOccured = false;
        set_error_handler(array(&$this, 'errorHandler'));

        $a = false;
        $this->packagexml = new PEAR_PackageFileManager_File($a, array('lang' => 'en'));
        PEAR::pushErrorHandling(PEAR_ERROR_CALLBACK, array(&$this, 'PEARerrorHandler'));
        $this->errorThrown = false;
        $this->_expectedMessage = 'NO ERROR TRIGGERED';
        $this->_expectedCode = -1;
        $this->_testMethod = 'unknown';
    }

    function tearDown()
    {
        unset($this->packagexml);
        PEAR::popErrorHandling();
    }

    function errorCodeToString($code)
    {
        $codes = array_flip(array(
            'OOPS' => -1,
            'PEAR_PACKAGEFILEMANAGER_NOSTATE' => PEAR_PACKAGEFILEMANAGER_NOSTATE,
            'PEAR_PACKAGEFILEMANAGER_NOVERSION' => PEAR_PACKAGEFILEMANAGER_NOVERSION,
            'PEAR_PACKAGEFILEMANAGER_NOPKGDIR' => PEAR_PACKAGEFILEMANAGER_NOPKGDIR,
            'PEAR_PACKAGEFILEMANAGER_NOBASEDIR' => PEAR_PACKAGEFILEMANAGER_NOBASEDIR,
            'PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND' => PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND,
            'PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE' => PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE,
            'PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE' => PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE,
            'PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE' => PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE,
            'PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE' => PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE,
            'PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE' => PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE,
            'PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST' => PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST,
            'PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES' => PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES,
            'PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST' => PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST,
            'PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS' => PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS,
            'PEAR_PACKAGEFILEMANAGER_NOPACKAGE' => PEAR_PACKAGEFILEMANAGER_NOPACKAGE,
            'PEAR_PACKAGEFILEMANAGER_WRONG_MROLE' => PEAR_PACKAGEFILEMANAGER_WRONG_MROLE,
            'PEAR_PACKAGEFILEMANAGER_NOSUMMARY' => PEAR_PACKAGEFILEMANAGER_NOSUMMARY,
            'PEAR_PACKAGEFILEMANAGER_NODESC' => PEAR_PACKAGEFILEMANAGER_NODESC,
            'PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS' => PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS,
            'PEAR_PACKAGEFILEMANAGER_NO_FILES' => PEAR_PACKAGEFILEMANAGER_NO_FILES,
            'PEAR_PACKAGEFILEMANAGER_IGNORED_EVERYTHING' => PEAR_PACKAGEFILEMANAGER_IGNORED_EVERYTHING,
            'PEAR_PACKAGEFILEMANAGER_INVALID_PACKAGE' => PEAR_PACKAGEFILEMANAGER_INVALID_PACKAGE,
            'PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE' => PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE,
            'PEAR_PACKAGEFILEMANAGER_INVALID_ROLE' => PEAR_PACKAGEFILEMANAGER_INVALID_ROLE,
            'PEAR_PACKAGEFILEMANAGER_PHP_NOT_PACKAGE' => PEAR_PACKAGEFILEMANAGER_PHP_NOT_PACKAGE
        ));
        return $codes[$code];
    }

    function _stripWhitespace($str)
    {
        return preg_replace('/\\s+/', '', $str);
    }

    function _methodExists($name) 
    {
        if (in_array(strtolower($name), get_class_methods($this->packagexml))) {
            return true;
        }
        $this->assertTrue(false, 'method '. $name . ' not implemented in ' . get_class($this->packagexml));
        return false;
    }

    function errorHandler($errno, $errstr, $errfile, $errline) {
        //die("$errstr in $errfile at line $errline: $errstr");
        $this->errorOccured = true;
        $this->assertTrue(false, "$errstr at line $errline, $errfile");
    }

    function PEARerrorHandler($error) {
        $this->assertEquals($this->_expectedCode, $error->getCode(),
            $this->_testMethod . ' ' . $this->errorCodeToString($this->_expectedCode)
            . ' actual: ' . $this->errorCodeToString($error->getCode()));
        $this->assertEquals($this->_expectedMessage, $error->getMessage(), $this->_testMethod);
        $this->errorThrown = 'true';
    }
    
    function expectPEARError($method, $msg, $code = null)
    {
        $this->_expectedMessage = $msg;
        $this->_expectedCode = $code;
        $this->_testMethod = $method;
    }
    
    function test_valid()
    {
        if (!$this->_methodExists('getFileList')) {
            return;
        }
        $this->packagexml->_options['addhiddenfiles'] = false;
        $this->packagexml->_options['packagefile'] = 'package.xml';
        $this->packagexml->_options['ignore'] =
        $this->packagexml->_options['include'] = false;
        $this->packagexml->_options['packagedirectory'] =
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/';
        $res = $this->packagexml->getFileList();
        $this->assertEquals(
            array(
                '/' =>
                array(
                'blarfoo' =>
                    array(
                        array(
                            'file' => 'blartest.txt',
                            'ext' => 'txt',
                            'path' => 'blarfoo/blartest.txt',
                            'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/blarfoo/blartest.txt',
                        )
                    ),
                'subfoo' =>
                    array(
                        0 =>
                            array(
                                'file' => 'test11.txt',
                                'ext' => 'txt',
                                'path' => 'subfoo/test11.txt',
                                'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/subfoo/test11.txt',
                            ),
                        1 =>
                            array(
                                'file' => 'test12.txt',
                                'ext' => 'txt',
                                'path' => 'subfoo/test12.txt',
                                'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/subfoo/test12.txt',
                            ),
                        'subsubfoo' =>
                            array(
                                array(
                                    'file' => 'boo.txt',
                                    'ext' => 'txt',
                                    'path' => 'subfoo/subsubfoo/boo.txt',
                                    'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/subfoo/subsubfoo/boo.txt',
                                )
                            ),
                        ),
                'testCVS' =>
                    array(
                        0 =>
                            array(
                                'file' => 'testEntries',
                                'ext' => '',
                                'path' => 'testCVS/testEntries',
                                'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/testCVS/testEntries',
                            ),
                            array(
                                'file' => 'testEntries.Extra',
                                'ext' => 'Extra',
                                'path' => 'testCVS/testEntries.Extra',
                                'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/testCVS/testEntries.Extra',
                            ),
                        ),
                0 =>
                    array(
                        'file' => 'test1.txt',
                        'ext' => 'txt',
                        'path' => 'test1.txt',
                        'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/test1.txt',
                    ),
                1 =>
                    array(
                        'file' => 'test2.txt',
                        'ext' => 'txt',
                        'path' => 'test2.txt',
                        'fullpath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'footest/test2.txt',
                    ),
                )
            ),
            $res,
            'incorrect dir structure');
        $this->assertFalse($this->errorThrown, 'error thrown');
    }
}

?>
