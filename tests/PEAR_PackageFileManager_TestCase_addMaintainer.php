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

class PEAR_PackageFileManager_TestCase_addMaintainer extends PHPUnit_TestCase
{
    /**
     * A PEAR_PackageFileManager object
     * @var        object
     */
    var $packagexml;

    function PEAR_PackageFileManager_TestCase_addMaintainer($name)
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
    
    function test_invalid_nosetoptions()
    {
        if (!$this->_methodExists('addMaintainer')) {
            return;
        }
        $this->expectPEARError('no setOptions() test',
            'PEAR_PackageFileManager Error: Run $managerclass->setOptions() before any other methods',
            PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
        );
        $this->packagexml->addMaintainer('peeber.php', 'ribbit', 'tadpole', 'frog');
        $this->assertEquals('true', $this->errorThrown, 'error not thrown');
    }
    
    function test_invalid_wrongrole()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        if (!$this->_methodExists('addMaintainer')) {
            return;
        }
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'File'));
        $this->assertFalse($this->errorThrown, 'error thrown');
        $this->expectPEARError('invalid role test',
            'PEAR_PackageFileManager Error: Maintainer role must be one of "' .
            implode(PEAR_Common::getUserRoles(), ', ') .
            '", was "ribbit"',
            PEAR_PACKAGEFILEMANAGER_WRONG_MROLE
        );
        $this->packagexml->addMaintainer('peeber.php', 'ribbit', 'tadpole', 'frog');
        $this->assertEquals('true', $this->errorThrown, 'error not thrown');
    }

    function test_valid_add()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        if (!$this->_methodExists('addMaintainer')) {
            return;
        }
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'File'));
        $this->assertFalse($this->errorThrown, 'error thrown');
        $this->packagexml->_packageXml['maintainers'] = array();
        $this->packagexml->addMaintainer('frog', 'lead', 'tadpole meister', 'frog@example.com');
        $this->assertFalse($this->errorThrown, 'error thrown');
        $this->assertEquals(
            array('handle' => 'frog', 'role' => 'lead',
                     'name' => 'tadpole meister', 'email' => 'frog@example.com'),
            $this->packagexml->_packageXml['maintainers'][0],
            'maintainers value wrong');
    }
    
    function test_valid_replace()
    {
        if (!$this->_methodExists('setOptions')) {
            return;
        }
        if (!$this->_methodExists('addMaintainer')) {
            return;
        }
        $this->packagexml->setOptions(array('state' => 'alpha', 'version' => '1.0',
            'packagedirectory' => dirname(__FILE__), 'baseinstalldir' => 'Foo',
            'packagefile' => 'test1_package.xml',
            'filelistgenerator' => 'File'));
        $this->assertFalse($this->errorThrown, 'error thrown');
        $this->packagexml->_packageXml['maintainers'] = array();
        $this->packagexml->addMaintainer('frog', 'lead', 'tadpole meister', 'frog@example.com');
        $this->assertFalse($this->errorThrown, 'error thrown');
        $this->assertEquals(
            array('handle' => 'frog', 'role' => 'lead',
                     'name' => 'tadpole meister', 'email' => 'frog@example.com'),
            $this->packagexml->_packageXml['maintainers'][0],
            'maintainers value wrong');
        $this->packagexml->addMaintainer('frog', 'developer', 'tadpole freak', 'frog@example.com');
        $this->assertFalse($this->errorThrown, 'error thrown');
        $this->assertEquals(
            array(array('handle' => 'frog', 'role' => 'developer',
                     'name' => 'tadpole freak', 'email' => 'frog@example.com')),
            $this->packagexml->_packageXml['maintainers'],
            'maintainers value wrong');
    }
}

?>
