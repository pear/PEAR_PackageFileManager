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

class PEAR_PackageFileManager_XMLOutput_TestCase_1 extends PHPUnit_TestCase
{
    /**
     * A PEAR_PackageFileManager object
     * @var        object
     */
    var $packagexml;

    function PEAR_PackageFileManager_CVS_TestCase_general($name)
    {
        $this->PHPUnit_TestCase($name);
    }

    function setUp()
    {
        error_reporting(E_ALL);
        $this->errorOccured = false;
        set_error_handler(array(&$this, 'errorHandler'));

        $a = false;
        $this->packagexml = new PEAR_PackageFileManager_XMLOutput;
        $this->errorThrown = false;
    }

    function tearDown()
    {
        unset($this->packagexml);
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
        if (error_reporting() == 0) {
            return;
        }
        //die("$errstr in $errfile at line $errline: $errstr");
        $this->errorOccured = true;
        $this->assertTrue(false, "$errstr at line $errline, $errfile");
    }
    
    function test_valid1()
    {
        if (!$this->_methodExists('_doFileList')) {
            return;
        }
        $arr = 
        array(
            '/' =>
            array(
                'baseinstalldir' => 'frunk',
                '##files' =>
                    array(
                        'tired' => array('role' => 'script'),
                        'first' =>
                            array(
                                '##files' =>
                                    array(
                                        'first.php' =>
                                            array(
                                                'role' => 'php',
                                                'install-as' => 'flop',
                                                'platform' => 'windows',
                                                'md5sum' => '25',
                                                'replacements' =>
                                                    array(
                                                        array(
                                                            'from' => 'blah',
                                                            'to' => 'version',
                                                            'type' => 'package-info'
                                                        )
                                                    ),
                                            ),
                                        'second.dat' =>
                                            array(
                                                'role' => 'data',
                                            ),
                                        'another' =>
                                            array(
                                                '##files' =>
                                                    array(
                                                        'third' =>
                                                            array(
                                                                'role' => 'test'
                                                            )
                                                    )
                                            )
                                    ),
                            ),
                        'second' =>
                            array(
                                '##files' =>
                                    array(
                                    'nested' =>
                                        array(
                                            '##files' =>
                                                array(
                                                    'another' => array(
                                                    'baseinstalldir' => '/')
                                                )
                                        )
                                    )
                            ),
                        // directory named files
                        'files' =>
                            array(
                                '##files' =>
                                    array(
                                        'wow' => array('role' => 'doc')
                                    )
                            ),
                    )
            )
        );
        $this->assertEquals(array(str_replace("\r", '', '      <dir baseinstalldir="frunk" name="/">
       <file role="script" name="tired"/>
       <dir name="first">
        <file role="php" md5sum="25" platform="windows" install-as="flop" name="first.php">
          <replace from="blah" to="version" type="package-info"/>
        </file>
        <file role="data" name="second.dat"/>
        <dir name="another">
         <file role="test" name="third"/>
        </dir> <!-- first/another -->
       </dir> <!-- first -->
       <dir name="second">
        <dir name="nested">
         <file baseinstalldir="/" name="another"/>
        </dir> <!-- second/nested -->
       </dir> <!-- second -->
       <dir name="files">
        <file role="doc" name="wow"/>
       </dir> <!-- files -->
      </dir> <!-- / -->
')), array($this->packagexml->_doFileList('', $arr, '/')));
    }
}

?>
