<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Package File Manager                                           |
// +------------------------------------------------------------------------+
// | Copyright (c) 2003 Gregory Beaver                                      |
// | Email         cellog@phpdoc.org                                        |
// +------------------------------------------------------------------------+
// | This source file is subject to version 3.00 of the PHP License,        |
// | that is available at http://www.php.net/license/3_0.txt.               |
// | If you did not receive a copy of the PHP license and are unable to     |
// | obtain it through the world-wide-web, please send a note to            |
// | license@php.net so we can mail you a copy immediately.                 |
// +------------------------------------------------------------------------+
// | Portions of this code based on phpDocumentor                           |
// | Web           http://www.phpdoc.org                                    |
// | Mirror        http://phpdocu.sourceforge.net/                          |
// +------------------------------------------------------------------------+
//

/**
 * @package PEAR_PackageFileManager
 */
/**
 * PEAR installer
 */
require_once 'PEAR/Common.php';
/**#@+
 * Error Codes
 */
define('PEAR_PACKAGEFILEMANAGER_NOSTATE', 1);
define('PEAR_PACKAGEFILEMANAGER_NOVERSION', 2);
define('PEAR_PACKAGEFILEMANAGER_NOPKGDIR', 3);
define('PEAR_PACKAGEFILEMANAGER_NOBASEDIR', 3);
define('PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND', 4);
define('PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE', 5);
define('PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE', 6);
define('PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE', 7);
define('PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE', 8);
define('PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE', 9);
define('PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST', 10);
define('PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES', 11);
define('PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST', 12);
define('PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS', 13);
define('PEAR_PACKAGEFILEMANAGER_NOPACKAGE', 14);
define('PEAR_PACKAGEFILEMANAGER_WRONG_MROLE', 15);
define('PEAR_PACKAGEFILEMANAGER_NOSUMMARY', 16);
define('PEAR_PACKAGEFILEMANAGER_NODESC', 17);
define('PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS', 18);
/**#@-*/
/**
 * Error messages
 * @global array $GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS']
 */
$GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS'] =
array(
    'en' =>
    array(
        PEAR_PACKAGEFILEMANAGER_NOSTATE =>
            'Release State (option \'state\') must by specified in PEAR_PackageFileManager setOptions (alpha|beta|stable)',
        PEAR_PACKAGEFILEMANAGER_NOVERSION =>
            'Release Version (option \'version\') must be specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_NOPKGDIR =>
            'Package source base directory (option \'packagedirectory\') must be ' .
            'specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_NOPKGDIR =>
            'Package install base directory (option \'baseinstalldir\') must be ' .
            'specified in PEAR_PackageFileManager setOptions',
        PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND =>
            'Base class "%s" can\'t be located',
        PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE =>
            'Base class "%s" can\'t be located in default or user-specified directories',
        PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE =>
            'Failed to write package.xml file to destination directory',
        PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE =>
            'Destination directory "%s" is unwritable',
        PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE =>
            'Failed to copy package.xml.tmp file to package.xml',
        PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE =>
            'Failed to open temporary file "%s" for writing',
        PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST =>
            'package.xml file path "%s" doesn\'t exist or isn\'t a directory',
        PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES =>
            'Directory "%s" is not a CVS directory (it must have the CVS/Entries file)',
        PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST =>
            'Package source base directory "%s" doesn\'t exist or isn\'t a directory',
        PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS =>
            'Run $managerclass->setOptions() before any other methods',
        PEAR_PACKAGEFILEMANAGER_NOPACKAGE =>
            'Package Name (option \'package\') must by specified in PEAR_PackageFileManager '.
            'setOptions to create a new package.xml',
        PEAR_PACKAGEFILEMANAGER_NOSUMMARY =>
            'Package Summary (option \'summary\') must by specified in PEAR_PackageFileManager' .
            ' setOptions to create a new package.xml',
        PEAR_PACKAGEFILEMANAGER_NODESC =>
            'Detailed Package Description (option \'description\') must be' .
            ' specified in PEAR_PackageFileManager constructor to create a new package.xml',
        PEAR_PACKAGEFILEMANAGER_WRONG_MROLE =>
            'Maintainer role must be one of "%s"',
        PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS =>
            'Add maintainers to a package before generating the package.xml',
        ),
        // other language translations go here
     );
/**
 * PEAR :: PackageGenerate updates the <filelist></filelist> section
 * of a PEAR package.xml file to reflect the current files in
 * preparation for a release.
 *
 * The PEAR_PackageGenerate class uses a plugin system to generate the
 * list of files in a package.  This allows both standard recursive
 * directory parsing (plugin type file) and more intelligent options
 * such as the CVS browser {@link PEAR_PackageFileManager_Cvs}, which
 * grabs all files in a local CVS checkout to create the list, ignoring
 * any other local files.
 *
 * Other options include specifying roles for file extensions (all .php
 * files are role="php", for example), roles for directories (all directories
 * named "tests" are given role="tests" by default), and exceptions.
 * Exceptions are specific pathnames with * and ? wildcards that match
 * a default role, but should have another.  For example, perhaps
 * a debug.tpl template would normally be data, but should be included
 * in the docs role.  Along these lines, to exclude files entirely,
 * use the ignore option.
 *
 * Required options for a release include version, baseinstalldir, state,
 * and packagedirectory (the full path to the local location of the
 * package to create a package.xml file for)
 *
 * Example usage:
 * <code>
 * <?php
 * require_once('PEAR/PackageFileManager.php');
 * $packagexml = new PEAR_PackageFileManager;
 * $e = $packagexml->setOptions(
 * array('baseinstalldir' => 'PhpDocumentor',
 *  'version' => '1.2.1',
 *  'packagedirectory' => 'C:/Web Pages/chiara/phpdoc2/',
 *  'state' => 'stable',
 *  'filelistgenerator' => 'cvs', // generate from cvs, use file for directory
 *  'notes' => 'We\'ve implemented many new and exciting features',
 *  'ignore' => array('TODO', 'tests/'), // ignore TODO, all files in tests/
 *  'installexceptions' => array('phpdoc' => '/*'), // baseinstalldir ="/" for phpdoc
 *  'dir_roles' => array('tutorials' => 'doc'),
 *  'exceptions' => array('README' => 'doc', // README would be data, now is doc
 *                        'PHPLICENSE.txt' => 'doc'))); // same for the license
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     die();
 * }
 * $packagexml->addRole('pkg', 'doc'); // add a new role mapping
 * $e = $packagexml->writePackageFile();
 * if (PEAR::isError($e)) {
 *     echo $e->getMessage();
 *     die();
 * }
 * ?>
 *
 * In addition, a package.xml file can now be generated from
 * scratch, with the usage of new options package, summary, description, and
 * the use of the {@link addMaintainer()} method
 * </code>
 * @package PEAR_PackageFileManager
 */
class PEAR_PackageFileManager
{
    /**
     * Format: array(array(regexp-ready string to search for whole path,
     * regexp-ready string to search for basename of ignore strings),...)
     * @var false|array
     * @access private
     */
    var $_ignore = false;
    
    /**
     * Contents of the package.xml file
     * @var string
     * @access private
     */
    var $_packageXml = false;
    
    /**
     * @access private
     * @var PEAR_Common
     */
    var $_pear;
    
    /**
     * @access private
     * @var string
     */
    var $_options = array(
                      'packagefile' => 'package.xml',
                      'filelistgenerator' => 'file',
                      'license' => 'PHP License',
                      'roles' =>
                        array(
            				'php' => 'php',
            				'html' => 'doc',
            				'*' => 'data',
                             ),
                      'dir_roles' =>
                        array(
        					'docs' => 'doc',
        					'examples' => 'doc',
        					'tests' => 'tests',
                             ),
                      'exceptions' => array(),
                      'installexceptions' => array(),
                      'ignore' => array(),
                      'deps' => array(),
                      'notes' => '',
                      'changelognotes' => false,
                      'outputdirectory' => false,
                      'pathtopackagefile' => false,
                      'lang' => 'en',
                      'configure_options' => array(),
                      );
    
    function PEAR_PackageFileManager()
    {
    }
    
    /**
     *
     * @param array
     */
    function setOptions($options = array())
    {
        if (!isset($options['state'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOSTATE);
        }
        if (!isset($options['version'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOVERSION);
        }
        if (!isset($options['packagedirectory'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOPKGDIR);
        } else {
            $options['packagedirectory'] = str_replace(DIRECTORY_SEPARATOR,
                                                     '/',
                                                     realpath($options['packagedirectory']));
            if ($options['packagedirectory']{strlen($options['packagedirectory']) - 1} != '/') {
                $options['packagedirectory'] .= '/';
            }
        }
        if (!isset($options['baseinstalldir'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOBASEDIR);
        }
        $this->_options = array_merge($this->_options, $options);
        
        $path = ($this->_options['pathtopackagefile'] ?
                    $this->_options['pathtopackagefile'] : $this->_options['packagedirectory']);
        $this->_options['filelistgenerator'] = ucfirst(strtolower($this->_options['filelistgenerator']));
        if (PEAR::isError($res = $this->_getExistingPackageXML($path, $this->_options['packagefile']))) {
            return $res;
        }
        // attempt to load the interface from the standard PEAR location
        @include_once('PEAR/PackageFileManager/' . $this->_options['filelistgenerator'] . '.php');
        if (!class_exists('PEAR_PackageFileManager_' . $this->_options['filelistgenerator'])) {
            if (isset($this->_options['usergeneratordir'])) {
                // attempt to load from a user-specified directory
                $this->_options['usergeneratordir'] = str_replace(DIRECTORY_SEPARATOR,
                                                                  '/',
                                                                  realpath($this->_options['usergeneratordir']));
                if ($this->_options['usergeneratordir']{strlen($this->_options['usergeneratordir']) - 1} != '/') {
                    $this->_options['usergeneratordir'] .= '/';
                }
                @include_once($this->_options['usergeneratordir'] . $this->_options['filelistgenerator'] . '.php');
                if (!class_exists('PEAR_PackageFileManager_' . $this->_options['filelistgenerator'])) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND_ANYWHERE,
                            'PEAR_PackageFileManager_' . $this->_options['filelistgenerator']);
                }
            } else {
                return $this->raiseError(PEAR_PACKAGEFILEMANAGER_GENERATOR_NOTFOUND,
                        'PEAR_PackageFileManager_' . $this->_options['filelistgenerator']);
            }
        }
    }
    
    /**
     * Add an extension/role mapping to the role mapping option
     * @param string file extension
     * @param string role
     */
    function addRole($extension, $role)
    {
        $this->_options['roles'][$extension] = $role;
    }
    
    /**
     * Add a maintainer to the list of maintainers.
     *
     * Every maintainer must have a valid account at pear.php.net.  The
     * first parameter is the account name (for instance, cellog is the
     * handle for Greg Beaver at pear.php.net).  Every maintainer has
     * one of four possible roles:
     * - lead: the primary maintainer
     * - developer: an important developer on the project
     * - contributor: self-explanatory
     * - helper: ditto
     *
     * Finally, specify the name and email of the maintainer
     * @param string username on pear.php.net of maintainer
     * @param lead|developer|contributor|helper role of maintainer
     * @param string full name of maintainer
     * @param string email address of maintainer
     */
    function addMaintainer($handle, $role, $name, $email)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!in_array($role, $GLOBALS['_PEAR_Common_maintainer_roles'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_WRONG_MROLE,
                implode(', ', $GLOBALS['_PEAR_Common_maintainer_roles']));
        }
        if (!isset($this->_packageXml['maintainers'])) {
            $this->_packageXml['maintainers'] = array();
        }
        $found = false;
        foreach($this->_packageXml['maintainers'] as $index => $maintainer) {
            if ($maintainer['handle'] == $handle) {
                $found = $index;
                break;
            }
        }
        $maintainer =
            array('handle' => $handle, 'role' => $role, 'name' => $name, 'email' => $email);
        if ($found !== false) {
            $this->_packageXml['maintainers'][$found] = $maintainer;
        } else {
            $this->_packageXml['maintainers'][] = $maintainer;
        }
    }
    
    /**
     * Add an install-time configuration option for building of source
     *
     * This option is only useful to PECL projects that are built upon
     * installation
     * @param string name of the option
     * @param string prompt to display to the user
     * @param string default value
     */
    function addConfigureOption($name, $prompt, $default = null)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!isset($this->_packageXml['configure_options'])) {
            $this->_packageXml['configure_options'] = array();
        }
        $found = false;
        foreach($this->_packageXml['configure_options'] as $index => $option) {
            if ($option['name'] == $name) {
                $found = $index;
                break;
            }
        }
        $option = array('name' => $name, 'prompt' => $prompt);
        if (isset($default)) {
            $option['default'] = $default;
        }
        if ($found !== false) {
            $this->_packageXml['configure_options'][$found] = $option;
        } else {
            $this->_packageXml['configure_options'][] = $option;
        }
    }
    
    /**
     * Add a dependency on another package, or an extension/php
     *
     * This will overwrite an existing dependency if it is found.  In
     * other words, if a dependency on PHP 4.1.0 exists, and
     * addDependency('php', '4.3.0', 'ge', 'php') is called, the existing
     * dependency on PHP 4.1.0 will be overwritten with the new one on PHP 4.3.0
     * @param string Dependency element name
     * @param string Dependency version
     * @param string A specific operator for the version, this can be one of:
     *   'has', 'not', 'lt', 'le', 'eq', 'ne', 'ge', or 'gt'
     * @param string Dependency type.  This can be one of:
     *   'pkg', 'ext', 'php', 'prog', 'os', 'sapi', or 'zend'
     */
    function addDependency($name, $version = false, $operator = 'ge', $type = 'pkg')
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        $found = false;
        foreach($this->_options['deps'] as $index => $dep) {
            if ($dep['name'] == $name && $deps['type'] == $type) {
                $found = $index;
                break;
            }
        }
        $dep =
            array(
                'name' => $name,
                'type' => $type);
        if ($version) {
            $dep['version'] = $version;
            if ($operator) {
                $dep['rel'] = $operator;
            }
        }

        if ($found !== false) {
            $this->_options['deps'][$found] = $dep; // overwrite existing dependency
        } else {
            $this->_options['deps'][] = $dep; // add new dependency
        }
    }

    /**
     * Writes the package.xml file out with the newly created <release></release> tag
     * @param boolean null if no debugging, true if web interface, false if command-line
     */
    function writePackageFile($debuginterface = null)
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!isset($this->_packageXml['maintainers'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS);
        }
        extract($this->_options);
        $date = date('Y-m-d');
        $this->_packageXml['release_date'] = $date;
        $this->_packageXml['version'] = $version;
        $this->_packageXml['release_state'] = $state;
        $this->_packageXml['release_notes'] = $notes;
        $this->_pear = new PEAR_Common;
        $this->_packageXml['filelist'] = $this->_getFileList();
        if (PEAR::isError($this->_packageXml['filelist'])) {
            return $this->_packageXml['filelist'];
        }
        if (isset($this->_pear->pkginfo['provides'])) {
            $this->_packageXml['provides'] = $this->_pear->pkginfo['provides'];
        }
        $this->_packageXml['release_deps'] = $this->_getDependencies();
        $this->_updateChangeLog();
        $common = &$this->_pear;
        $packagexml = $common->xmlFromInfo($this->_packageXml);
        if (isset($debuginterface)) {
            if ($debuginterface) {
                echo '<pre>' . htmlentities($packagexml) . '</pre>';
            } else {
                echo $packagexml;
            }
            return true;
        }
        $outputdir = ($this->_options['outputdirectory'] ?
                        $this->_options['outputdirectory'] : $this->_options['packagedirectory']);
        if (is_writable($this->_options['packagedirectory'] . $this->_options['packagefile'])) {
            if ($fp = @fopen($outputdir . $this->_options['packagefile'] . '.tmp', "w")) {
                $written = @fwrite($fp, $packagexml);
                @fclose($fp);
                if ($written === false) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE);
                }
                if (!@copy($outputdir . $this->_options['packagefile'] . '.tmp',
                        $outputdir . $this->_options['packagefile'])) {
                    return $this->raiseError(PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE);
                } else {
                    @unlink($outputdir . $this->_options['packagefile'] . '.tmp');
                    return true;
                }
            } else {
                return $this->raiseError(PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE,
                    $outputdir . $this->_options['packagefile'] . '.tmp');
            }
        } else {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE, $outputdir);
        }
    }
    
    /**
     * ALWAYS use this to test output before overwriting your package.xml!!
     * @uses writePackageFile() calls with the debug parameter set based on
     *       whether it is called from the command-line or web interface
     */
    function debugPackageFile()
    {
        $webinterface = isset($_SERVER['PATH_TRANSLATED']);
        return $this->writePackageFile($webinterface);
    }
    
    /**
     * Utility function to shorten error generation code
     * @static
     */
    function raiseError($code, $i1 = '', $i2 = '')
    {
        return PEAR::raiseError('PEAR_PackageFileManager Error: ' .
                    sprintf($GLOBALS['_PEAR_PACKAGEFILEMANAGER_ERRORS'][$this->_options['lang']][$code],
                    $i1, $i2));
    }
    
    /**
     * Uses {@link PEAR_Common::analyzeSourceCode()} and {@link PEAR_Common::buildProvidesArray()}
     * to create the <provides></provides> section of the package.xml
     * @param PEAR_Common
     * @param string path to source file
     * @access private
     */
    function _addProvides(&$pear, $file)
    {
        $pear->buildProvidesArray($pear->analyzeSourceCode($file));
    }
    
    /**
     * @uses getDirTag() generate the xml from the array
     * @return string
     * @access private
     */
    function _getFileList()
    {
        $generatorclass = 'PEAR_PackageFileManager_' . $this->_options['filelistgenerator'];
        $generator = new $generatorclass($this, $this->_options);
        return $this->_getDirTag($generator->getFileList()); 
    }
    
    /**
     * Recursively generate the <filelist> section's <dir> and <file> tags
     * @param array the sorted directory structure
     * @param false|string whether the parent directory has a role this should
     * inherit
     * @param integer indentation level
     * @access private
     */
    function _getDirTag($struc, $role=false)
    {
        if (PEAR::isError($struc)) {
            return $struc;
        }
        extract($this->_options);
        $ret = array();
    	foreach($struc as $dir => $files) {
    		if ($dir === '/') {
                return $this->_getDirTag($struc[$dir], $role);
    		} else {
    			if (!isset($files['file'])) {
    				$myrole = '';
    				if ($role) {
                        $myrole = $role;
                    } elseif (isset($dir_roles[$dir])) {
                        $myrole = $dir_roles[$dir];
                    }
                    $ret = array_merge($ret, $this->_getDirTag($files, $myrole));
    			} else {
    				$myrole = '';
    				if (!$role)
    				{
    					$myrole = false;
    					if (isset($exceptions[$files['file']])) {
    						$myrole = $exceptions[$files['file']];
    					} elseif (isset($roles[$files['ext']])) {
    						$myrole = $roles[$files['ext']];
    					} else {
                            $myrole = $roles['*'];
                        }
    				} else {
                        $myrole = $role;
                    }
                    if (isset($installexceptions[$files['file']])) {
                        $bi = $installexceptions[$files['file']];
                    } else {
                        $bi = $baseinstalldir;
                    }
    				$ret[$files['path']] = array('role' => $myrole, 'baseinstalldir' => $bi);
                    if ($myrole == 'php') {
                        $this->_addProvides($this->_pear, $files['fullpath']);
                    }
    			}
    		}
    	}
    	return $ret;
    }

    /**
     * Retrieve the 'deps' option passed to the constructor
     * @access private
     * @return string
     */
    function _getDependencies()
    {
        if (isset($this->_options['deps'])) {
            return $this->_options['deps'];
        } else {
            return array();
        }
    }

    /**
     * Creates a changelog entry with the current release
     * notes and dates, or overwrites a previous creation
     * @access private
     */
    function _updateChangeLog()
    {
        $curlog = false;
        foreach($this->_packageXml['changelog'] as $index => $changelog) {
            if ($changelog['version'] == $this->_options['version']) {
                $curlog = $index;
            }
        }
        $notes = ($this->_options['changelognotes'] ?
                    $this->_options['changelognotes'] : $this->_options['notes']);
        $changelog = array('version' => $this->_options['version'],
                           'release_date' => date('Y-m-d'),
                           'release_license' => $this->_options['license'],
                           'release_state' => $this->_options['state'],
                           'release_notes' => $notes,
                           );
        if ($curlog !== false) {
            $this->_packageXml['changelog'][$curlog] = $changelog;
        } else {
            $this->_packageXml['changelog'][] = $changelog;
        }
    }

    /**
     * @access private
     */
    function _getExistingPackageXML($path, $packagefile = 'package.xml')
    {
        if (@is_dir($path)) {
            $contents = @file_get_contents($path . $packagefile);
            if (!$contents) {
                return $this->_generateNewPackageXML();
            } else {
                $common = new PEAR_Common;
                $this->_packageXml = $common->infoFromString($contents);
                if (PEAR::isError($this->_packageXml)) {
                    return $this->_packageXml;
                }
                unset($this->_packageXml['filelist']);
            }
            return true;
        } else {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_PATH_DOESNT_EXIST,
                $path);
        }
    }
    
    /**
     * Create the structure for a new package.xml
     * @access private
     */
    function _generateNewPackageXML()
    {
        if (!isset($this->_options['package'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOPACKAGE);
        }
        if (!isset($this->_options['summary'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NOSUMMARY);
        }
        if (!isset($this->_options['description'])) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_NODESC);
        }
        $this->_packageXml = array();
        $this->_packageXml['package'] = $this->_options['package'];
        $this->_packageXml['summary'] = $this->_options['summary'];
        $this->_packageXml['description'] = $this->_options['description'];
        $this->_packageXml['changelog'] = array();
        return true;
    }
}

if (!function_exists('file_get_contents')) {
function file_get_contents($path, $use_include_path = null, $context = null)
{
    $a = @file($path, $use_include_path, $context);
    if (is_array($a)) {
        return implode('', $a);
    } else {
        return false;
    }
}
}
?>
