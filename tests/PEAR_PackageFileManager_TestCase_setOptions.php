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

class PEAR_PackageFileManager_TestCase_setOptions extends PHPUnit_TestCase
{
    /**
     * A PEAR_PackageFileManager object
     * @var        object
     */
    var $packagexml;

    function PEAR_PackageFileManager_TestCase_setOptions($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL);
        $this->errorOccured = false;
        set_error_handler(array(&$this, 'errorHandler'));

        $this->packagexml = new PEAR_PackageFileManager;
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
    
    function test_invalid_nostate()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid nostate',
            'PEAR_PackageFileManager Error: Release State (option \'state\') ' .
            'must by specified in PEAR_PackageFileManager setOptions (alpha|' .
            'beta|stable)', PEAR_PACKAGEFILEMANAGER_NOSTATE);
        $this->packagexml->setOptions(array());
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_invalid_noversion()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid noversion',
            'PEAR_PackageFileManager Error: Release Version (option \'version\')' .
            ' must be specified in PEAR_PackageFileManager setOptions', PEAR_PACKAGEFILEMANAGER_NOVERSION);
        $this->packagexml->setOptions(array('state' => 'alpha'));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_invalid_nopackagedir()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid nopackagedir',
            'PEAR_PackageFileManager Error: Package source base directory (option \'packagedirectory\') must be ' .
            'specified in PEAR_PackageFileManager setOptions', PEAR_PACKAGEFILEMANAGER_NOPKGDIR);
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0'));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_invalid_nobaseinstalldir()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid nobaseinstalldir',
            'PEAR_PackageFileManager Error: Package install base directory (option \'baseinstalldir\') must be ' .
            'specified in PEAR_PackageFileManager setOptions', PEAR_PACKAGEFILEMANAGER_NOBASEDIR);
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__)));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_invalid_badfilelistgenerator1()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid badfilelistgenerator1',
            'PEAR_PackageFileManager Error: Base class "PEAR_PackageFileManager_Gronk"' .
            ' can\'t be located', PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND);
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'Gronk'));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_invalid_badfilelistgenerator2()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid badfilelistgenerator2',
            'PEAR_PackageFileManager Error: Base class "PEAR_PackageFileManager_Gronk"' .
            ' can\'t be located in default or user-specified directories',
            PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE);
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'Gronk',
            'usergeneratordir' => '\\onk'));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_invalid_badfilelistgenerator3()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid badfilelistgenerator3',
            'PEAR_PackageFileManager Error: Base class "PEAR_PackageFileManager_Gronk"' .
            ' can\'t be located in default or user-specified directories',
            PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE);
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'Gronk',
            'usergeneratordir' => dirname(__FILE__)));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
    
    function test_valid_filelistgeneratorfile()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'File'));
        $this->assertFalse($this->errorThrown, 'error thrown');
    }
    
    function test_valid_filelistgeneratorcvs()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'CVS'));
        $this->assertFalse($this->errorThrown, 'error thrown');
    }
    
    function test_valid_filelistgeneratorcustom()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'Test_file',
            'usergeneratordir' => dirname(__FILE__)
            ));
        $this->assertFalse($this->errorThrown, 'error thrown');
    }
    
    function test_invalid_filelistgeneratorcustom()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        $this->expectPEARError('invalid badfilelistgenerator3',
            'PEAR_PackageFileManager Error: Base class "PEAR_PackageFileManager_Bad_file"' .
            ' can\'t be located in default or user-specified directories',
            PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE);
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'Bad_file',
            'usergeneratordir' => dirname(__FILE__)
            ));
        $this->assertEquals('true', $this->errorThrown, 'no error thrown');
    }
}

?>
