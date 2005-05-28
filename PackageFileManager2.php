<?php
/**
 * Here is a sample file that demonstrates all of PEAR_PackageFile2Manager's features.
 *
 * First, a subpackage is created that is then automatically processed with the parent package
 * Next, the parent package is created.  Finally, a compatible PEAR_PackageFileManager object is
 * automatically created from the parent package in order to maintain two copies of the same file.
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR_PackageFile2Manager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_PackageFile2Manager
 * @since      File available since Release 0.1.0
 */
/**
 * PEAR Packagefile parser
 */
require_once 'PEAR/PackageFile.php';
/**
 * PEAR Packagefile version 2.0
 */
require_once 'PEAR/PackageFile/v2/rw.php';
/**#@+
 * Error Codes
 */
define('PEAR_PACKAGEFILE2MANAGER_NOPKGDIR', 3);
define('PEAR_PACKAGEFILE2MANAGER_NOBASEDIR', 4);
define('PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND', 5);
define('PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND_ANYWHERE', 6);
define('PEAR_PACKAGEFILE2MANAGER_CANTWRITE_PKGFILE', 7);
define('PEAR_PACKAGEFILE2MANAGER_DEST_UNWRITABLE', 8);
define('PEAR_PACKAGEFILE2MANAGER_CANTCOPY_PKGFILE', 9);
define('PEAR_PACKAGEFILE2MANAGER_CANTOPEN_TMPPKGFILE', 10);
define('PEAR_PACKAGEFILE2MANAGER_PATH_DOESNT_EXIST', 11);
define('PEAR_PACKAGEFILE2MANAGER_NOCVSENTRIES', 12);
define('PEAR_PACKAGEFILE2MANAGER_DIR_DOESNT_EXIST', 13);
define('PEAR_PACKAGEFILE2MANAGER_RUN_SETOPTIONS', 14);
define('PEAR_PACKAGEFILE2MANAGER_NO_FILES', 20);
define('PEAR_PACKAGEFILE2MANAGER_IGNORED_EVERYTHING', 21);
define('PEAR_PACKAGEFILE2MANAGER_INVALID_PACKAGE', 22);
define('PEAR_PACKAGEFILE2MANAGER_INVALID_REPLACETYPE', 23);
define('PEAR_PACKAGEFILE2MANAGER_CVS_PACKAGED', 26);
define('PEAR_PACKAGEFILE2MANAGER_NO_PHPCOMPATINFO', 27);
/**#@-*/
/**
 * Error messages
 * @global array $GLOBALS['_PEAR_PACKAGEFILE2MANAGER_ERRORS']
 * @access private
 */
$GLOBALS['_PEAR_PACKAGEFILE2MANAGER_ERRORS'] =
array(
    'en' =>
    array(
        PEAR_PACKAGEFILE2MANAGER_NOPKGDIR =>
            'Package source base directory (option \'packagedirectory\') must be ' .
            'specified in PEAR_PackageFile2Manager setOptions',
        PEAR_PACKAGEFILE2MANAGER_NOBASEDIR =>
            'Package install base directory (option \'baseinstalldir\') must be ' .
            'specified in PEAR_PackageFile2Manager setOptions',
        PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND =>
            'Base class "%s" can\'t be located',
        PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND_ANYWHERE =>
            'Base class "%s" can\'t be located in default or user-specified directories',
        PEAR_PACKAGEFILE2MANAGER_CANTWRITE_PKGFILE =>
            'Failed to write package.xml file to destination directory',
        PEAR_PACKAGEFILE2MANAGER_DEST_UNWRITABLE =>
            'Destination directory "%s" is unwritable',
        PEAR_PACKAGEFILE2MANAGER_CANTCOPY_PKGFILE =>
            'Failed to copy package.xml.tmp file to package.xml',
        PEAR_PACKAGEFILE2MANAGER_CANTOPEN_TMPPKGFILE =>
            'Failed to open temporary file "%s" for writing',
        PEAR_PACKAGEFILE2MANAGER_PATH_DOESNT_EXIST =>
            'package.xml file path "%s" doesn\'t exist or isn\'t a directory',
        PEAR_PACKAGEFILE2MANAGER_NOCVSENTRIES =>
            'Directory "%s" is not a CVS directory (it must have the CVS/Entries file)',
        PEAR_PACKAGEFILE2MANAGER_DIR_DOESNT_EXIST =>
            'Package source base directory "%s" doesn\'t exist or isn\'t a directory',
        PEAR_PACKAGEFILE2MANAGER_RUN_SETOPTIONS =>
            'Run $managerclass->setOptions() before any other methods',
        PEAR_PACKAGEFILE2MANAGER_NO_FILES =>
            'No files found, check the path "%s"',
        PEAR_PACKAGEFILE2MANAGER_IGNORED_EVERYTHING =>
            'No files left, check the path "%s" and ignore option "%s"',
        PEAR_PACKAGEFILE2MANAGER_INVALID_PACKAGE =>
            'Package validation failed:%s%s',
        PEAR_PACKAGEFILE2MANAGER_INVALID_REPLACETYPE =>
            'Replacement Type must be one of "%s", was passed "%s"',
        PEAR_PACKAGEFILE2MANAGER_CVS_PACKAGED =>
            'path "%path%" contains CVS directory',
        PEAR_PACKAGEFILE2MANAGER_NO_PHPCOMPATINFO =>
            'PHP_Compat is not installed, cannot detect dependencies',
       ),
        // other language translations go here
     );
/**
 * PEAR :: PackageFile2Manager, like PEAR_PackageFileManager, is designed to
 * create and manipulate package.xml files.
 *
 * The PEAR_PackageFile2Manager class can work directly with PEAR_PackageFileManager
 * to create parallel package.xml files, version 1.0 and 2.0, that represent the
 * same project, but take advantage of package.xml 2.0-specific features.
 *
 * Like PEAR_PackageFileManager, The PEAR_PackageFile2Manager class uses a plugin system
 * to generate the list of files in a package.  This allows both standard recursive
 * directory parsing (plugin type file) and more intelligent options
 * such as the CVS browser {@link PEAR_PackageFile2Manager_Cvs}, which
 * grabs all files in a local CVS checkout to create the list, ignoring
 * any other local files.
 *
 * Example usage is similar to PEAR_PackageFileManager:
 * <code>
 * <?php
 * require_once('PEAR/PackageFile2Manager.php');
 * PEAR::setErrorHandling(PEAR_ERROR_DIE);
 * //require_once 'PEAR/Config.php';
 * //PEAR_Config::singleton('/path/to/unusualpearconfig.ini');
 * // use the above lines if the channel information is not validating
 * $packagexml = new PEAR_PackageFile2Manager;
 * $e = $packagexml->setOptions(
 * array('baseinstalldir' => 'PhpDocumentor',
 *  'packagedirectory' => 'C:/Web Pages/chiara/phpdoc2/',
 *  'filelistgenerator' => 'cvs', // generate from cvs, use file for directory
 *  'ignore' => array('TODO', 'tests/'), // ignore TODO, all files in tests/
 *  'installexceptions' => array('phpdoc' => '/*'), // baseinstalldir ="/" for phpdoc
 *  'dir_roles' => array('tutorials' => 'doc'),
 *  'exceptions' => array('README' => 'doc', // README would be data, now is doc
 *                        'PHPLICENSE.txt' => 'doc'))); // same for the license
 * $packagexml->setPackage('MyPackage');
 * $packagexml->setChannel('mychannel.example.com');
 * $packagexml->setAPIVersion('1.0.0');
 * $packagexml->setReleaseVersion('1.2.1');
 * $packagexml->setReleaseStability('stable');
 * $packagexml->setAPIStability('stable');
 * $packagexml->setNotes("We've implemented many new and exciting features");
 * $packagexml->setPackageType('php'); // this is a PEAR-style php script package
 * $packagexml->addRelease(); // set up a release section
 * $packagexml->setOSInstallCondition('windows');
 * $packagexml->addInstallAs('pear-phpdoc.bat', 'phpdoc.bat');
 * $packagexml->addIgnore('pear-phpdoc');
 * $packagexml->addRelease(); // add another release section for all other OSes
 * $packagexml->addInstallAs('pear-phpdoc', 'phpdoc');
 * $packagexml->addIgnore('pear-phpdoc.bat');
 * $packagexml->addRole('pkg', 'doc'); // add a new role mapping
 * $packagexml->setPhpDep('4.2.0');
 * $packagexml->setPearinstallerDep('1.4.0a12');
 * $packagexml->addMaintainer('lead', 'cellog', 'Greg Beaver', 'cellog@php.net');
 * $packagexml->setLicense('PHP License', 'http://www.php.net/license');
 * $packagexml->generateContents(); // create the <contents> tag
 * // replace @PHP-BIN@ in this file with the path to php executable!  pretty neat
 * $test->addReplacement('pear-phpdoc', 'pear-config', '@PHP-BIN@', 'php_bin');
 * $test->addReplacement('pear-phpdoc.bat', 'pear-config', '@PHP-BIN@', 'php_bin');
 * $pkg = &$packagexml->exportCompatiblePackageFile1(); // get a PEAR_PackageFile object
 * // note use of {@link debugPackageFile()} - this is VERY important
 * if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
 *     $pkg->writePackageFile();
 *     $packagexml->writePackageFile();
 * } else {
 *     $pkg->debugPackageFile();
 *     $packagexml->debugPackageFile();
 * }
 * ?>
 * </code>
 * 
 * In addition, a package.xml file can now be generated from
 * scratch, with the usage of new options package, summary, description, and
 * the use of the {@link addLead(), addDeveloper(), addContributor(), addHelper()} methods
 * @category   pear
 * @package    PEAR_PackageFile2Manager
 * @author     Greg Beaver <cellog@php.net>
 * @copyright  2005 The PHP Group
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    Release: @PEAR-VER@
 * @link       http://pear.php.net/package/PEAR
 * @since      Class available since Release 1.4.0a1
 */
class PEAR_PackageFile2Manager extends PEAR_PackageFile_v2_rw
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
     * @var PEAR_PackageFile_v2
     * @access private
     */
    var $_packageXml = false;
    
    /**
     * Contents of the original package.xml file, if any
     * @var PEAR_PackageFile_v2
     * @access private
     */
    var $_oldPackageXml = false;
    
    /**
     * @access private
     * @var array
     */
    var $_warningStack = array();

    /**
     * flag used to determine whether to use PHP_CompatInfo to detect deps
     * @var boolean
     * @access private
     */
    var $_detectDependencies = false;

    /**
     * The original contents of the old package.xml, if any
     * @var PEAR_PackageFile_v2|false
     * @access private
     */
    var $_old = false;

    /**
     * Collection of subpackages
     *
     * This collection is used to handle coordination between the contents of
     * related packages whose files reside in the same development directory
     * @var array
     * @access private
     */
    var $_subpackages = array();

    /**
     * @access private
     * @var string
     */
    var $_options = array(
                      'packagefile' => 'package.xml',
                      'filelistgenerator' => 'file',
                      'license' => 'PHP License',
                      'changelogoldtonew' => true,
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
        					'tests' => 'test',
                             ),
                      'exceptions' => array(),
                      'installexceptions' => array(),
                      'ignore' => array(),
                      'include' => false,
                      'notes' => '',
                      'changelognotes' => false,
                      'outputdirectory' => false,
                      'pathtopackagefile' => false,
                      'lang' => 'en',
                      'configure_options' => array(),
                      'replacements' => array(),
                      'simpleoutput' => false,
                      'addhiddenfiles' => false,
                      );
    
    /**
     * Does nothing, use factory
     *
     * The constructor is not used in order to be able to
     * return a PEAR_Error from setOptions
     * @see setOptions()
     */
    function PEAR_PackageFile2Manager()
    {
        parent::PEAR_PackageFile_v2();
        $config = &PEAR_Config::singleton();
        $this->setConfig($config);
    }
    
    /**
     * Set package.xml generation options
     *
     * The options array is indexed as follows:
     * <code>
     * $options = array('option_name' => <optionvalue>);
     * </code>
     *
     * The documentation below simplifies this description through
     * the use of option_name without quotes
     *
     * Configuration options:
     * - lang: lang controls the language in which error messages are
     *         displayed.  There are currently only English error messages,
     *         but any contributed will be added over time.<br>
     *         Possible values: en (default)
     * - packagefile: the name of the packagefile, defaults to package.xml
     * - pathtopackagefile: the path to an existing package file to read in,
     *                      if different from the packagedirectory
     * - packagedirectory: the path to the base directory of the package.  For
     *                     package PEAR_PackageFileManager, this path is
     *                     /path/to/pearcvs/pear/PEAR_PackageFileManager where
     *                     /path/to/pearcvs is a local path on your hard drive
     * - outputdirectory: the path in which to place the generated package.xml
     *                    by default, this is ignored, and the package.xml is
     *                    created in the packagedirectory
     * - filelistgenerator: the <filelist> section plugin which will be used.
     *                      In this release, there are two generator plugins,
     *                      file and cvs.  For details, see the docs for these
     *                      plugins
     * - usergeneratordir: For advanced users.  If you write your own filelist
     *                     generator plugin, use this option to tell
     *                     PEAR_PackageFileManager where to find the file that
     *                     contains it.  If the plugin is named foo, the class
     *                     must be named PEAR_PackageFileManager_Foo
     *                     no matter where it is located.  By default, the Foo
     *                     plugin is located in PEAR/PackageFileManager/Foo.php.
     *                     If you pass /path/to/foo in this option, setOptions
     *                     will look for PEAR_PackageFileManager_Foo in
     *                     /path/to/foo/Foo.php
     * - changelogoldtonew: True if the ChangeLog should list from oldest entry to
     *                      newest.  Set to false if you would like new entries first
     * - simpleoutput: True if the package.xml should be human-readable
     * - addhiddenfiles: True if you wish to add hidden files/directories that begin with .
     *                   like .bashrc.  This is only used by the File generator.  The CVS
     *                   generator will use all files in CVS regardless of format
     *
     * package.xml simple options:
     * - baseinstalldir: The base directory to install this package in.  For
     *                   package PEAR_PackageFileManager, this is "PEAR", for
     *                   package PEAR, this is "/"
     * - changelognotes: notes for the changelog, this should be more detailed than
     *                   the release notes.  By default, PEAR_PackageFileManager uses
     *                   the notes option for the changelog as well
     *
     * <b>WARNING</b>: all complex options that require a file path are case-sensitive
     *
     * package.xml complex options:
     * - ignore: an array of filenames, directory names, or wildcard expressions specifying
     *           files to exclude entirely from the package.xml.  Wildcards are operating system
     *           wildcards * and ?.  file*foo.php will exclude filefoo.php, fileabrfoo.php and
     *           filewho_is_thisfoo.php.  file?foo.php will exclude fileafoo.php and will not
     *           exclude fileaafoo.php.  test/ will exclude all directories and subdirectories of
     *           ANY directory named test encountered in directory parsing.  *test* will exclude
     *           all files and directories that contain test in their name
     * - include: an array of filenames, directory names, or wildcard expressions specifying
     *            files to include in the listing.  All other files will be ignored.
     *            Wildcards are in the same format as ignore
     * - roles: this is an array mapping file extension to install role.  This
     *          specifies default behavior that can be overridden by the exceptions
     *          option and dir_roles option.  use {@link addRole()} to add a new
     *          role to the pre-existing array
     * - dir_roles: this is an array mapping directory name to install role.  All
     *              files in a directory whose name matches the directory will be
     *              given the install role specified.  Single files can be excluded
     *              from this using the exceptions option.  The directory should be
     *              a relative path from the baseinstalldir, or "/" for the baseinstalldir
     * - exceptions: specify file role for specific files.  This array maps all files
     *               matching the exact name of a file to a role as in "file.ext" => "role"
     * - globalreplacements: a list of replacements that should be performed on every single file.
     *                       The format is the same as replacements
     * @see PEAR_PackageFileManager_File
     * @see PEAR_PackageFileManager_CVS
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILE2MANAGER_NOPKGDIR
     * @throws PEAR_PACKAGEFILE2MANAGER_NOBASEDIR
     * @throws PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND_ANYWHERE
     * @throws PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND
     * @param array
     */
    function setOptions($options = array(), $internal = false)
    {
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
        if (isset($options['pathtopackagefile'])) {
            $options['pathtopackagefile'] = str_replace(DIRECTORY_SEPARATOR,
                                                     '/',
                                                     realpath($options['pathtopackagefile']));
            if ($options['pathtopackagefile']{strlen($options['pathtopackagefile']) - 1} != '/') {
                $options['pathtopackagefile'] .= '/';
            }
        }
        if (!isset($options['baseinstalldir'])) {
            return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_NOBASEDIR);
        }
        $this->_options = array_merge($this->_options, $options);
        
        $path = ($this->_options['pathtopackagefile'] ?
                    $this->_options['pathtopackagefile'] : $this->_options['packagedirectory']);
        $this->_options['filelistgenerator'] =
            ucfirst(strtolower($this->_options['filelistgenerator']));
        if (!$internal) {
            if (PEAR::isError($res = $this->_getExistingPackageXML($path,
                  $this->_options['packagefile']))) {
                return $res;
            }
        }
        if (!class_exists('PEAR_PackageFileManager_' . $this->_options['filelistgenerator'])) {
            // attempt to load the interface from the standard PEAR location
            if ($this->isIncludeable('PEAR/PackageFileManager/' . $this->_options['filelistgenerator'] . '.php')) {
                include_once('PEAR/PackageFileManager/' . $this->_options['filelistgenerator'] . '.php');
            } elseif (isset($this->_options['usergeneratordir'])) {
                // attempt to load from a user-specified directory
                if (is_dir(realpath($this->_options['usergeneratordir']))) {
                    $this->_options['usergeneratordir'] =
                        str_replace(DIRECTORY_SEPARATOR,
                                    '/',
                                    realpath($this->_options['usergeneratordir']));
                    if ($this->_options['usergeneratordir']{strlen($this->_options['usergeneratordir']) - 1} != '/') {
                        $this->_options['usergeneratordir'] .= '/';
                    }
                } else {
                    $this->_options['usergeneratordir'] = '////';
                }
                if (file_exists($this->_options['usergeneratordir'] .
                      $this->_options['filelistgenerator'] . '.php') &&
                      is_readable($this->_options['usergeneratordir'] .
                      $this->_options['filelistgenerator'] . '.php')) {
                    include_once($this->_options['usergeneratordir'] .
                        $this->_options['filelistgenerator'] . '.php');
                }
                if (!class_exists('PEAR_PackageFileManager_' . $this->_options['filelistgenerator'])) {
                    return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND_ANYWHERE,
                            'PEAR_PackageFileManager_' . $this->_options['filelistgenerator']);
                }
            } else {
                return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_GENERATOR_NOTFOUND,
                        'PEAR_PackageFileManager_' . $this->_options['filelistgenerator']);
            }
        }
    }

    /**
     * Define a link between a subpackage and the parent package
     *
     * In many cases, a subpackage is developed in the same directory
     * as the parent package, and the files should be excluded from the package.xml
     * version 2.0.
     * @param PEAR_PackageFile2Manager object representing the subpackage's package.xml
     * @param boolean dependency type to add, use true for a package dependency, false for a
                      subpackage dependency
     * @param boolean whether the dependency should be required or optional
     */
    function specifySubpackage(&$pm, $dependency = null, $required = false)
    {
        if (!$pm->getDate()) {
            $pm->setDate(date('Y-m-d'));
        }
        if (!$pm->validate(PEAR_VALIDATE_NORMAL)) {
            return false;
        }
        $this->_subpackages[] = &$pm;
        if ($dependency !== null) {
            if ($required) {
                $type = 'required';
            } else {
                $type = 'optional';
            }
            if ($pm->getChannel()) {
                if ($dependency) {
                    $this->addPackageDepWithChannel($type, $pm->getPackage(), $pm->getChannel(),
                        $pm->getVersion(), false, false, false, $pm->getProvidesExtension());
                } else {
                    $this->addSubPackageDepWithChannel($type, $pm->getPackage(), $pm->getChannel(),
                        $pm->getVersion(), false, false, false, $pm->getProvidesExtension());
                }
            } else {
                if ($dependency) {
                    $this->addPackageDepWithUri($type, $pm->getPackage(), $pm->getUri(),
                        $this->getProvidesExtension());
                } else {
                    $this->addSubpackageDepWithUri($type, $pm->getPackage(), $pm->getUri(),
                        $this->getProvidesExtension());
                }
            }
        }
    }

    /**
     * Return a PEAR_PackageFileManager object that can be used to generate a
     * fully compatible package.xml version 1.0
     *
     * This simplifies the maintenance of both a package.xml version 1.0 and version 2.0,
     * as a single script can be used to maintain both package.xml copies.  Use this
     * after all settings have been applied to the version 2.0 class
     *
     * If the default packagefile used is package.xml, it will be automatically changed
     * to package2.xml, and the package.xml version 1.0 will be output as package.xml.
     * This simplifies the process of automatic packaging
     * @return PEAR_PackageFileManager
     */
    function &exportCompatiblePackageFile1($options = array())
    {
        if ($this->_options['packagefile'] == 'package.xml') {
            $this->_options['packagefile'] = 'package2.xml';
        }
        require_once 'PEAR/PackageFileManager.php';
        $greplace = $replaces = false;
        if (@$this->_options['globalreplacements']) {
            $greplace = array();
            foreach ($this->_options['globalreplacements'] as $replaceobj) {
                $atts = $replaceobj->getXml();
                $greplace[] = $atts['attribs'];
            }
        }
        if (@$this->_options['replacements']) {
            $replaces = array();
            foreach ($this->_options['replacements'] as $path => $replaces) {
                foreach ($replaces as $replaceobj) {
                    $atts = $replaceobj->getXml();
                    $replaces[$path][] = $atts['attribs'];
                }
            }
        }
        $pf = new PEAR_PackageFileManager;
        $pf->setOptions(array_merge($this->_options, array(
                'packagefile' => 'package.xml',
                'package' => $this->getPackage(),
                'version' => $this->getVersion(),
                'license' => $this->getLicense(),
                'notes' => $this->getNotes(),
                'changelognotes' => $this->_options['changelognotes'],
                'summary' => $this->getSummary(),
                'description' => $this->getDescription(),
                'baseinstalldir' => $this->_options['baseinstalldir'],
                'maintainers' => array(),
                'cleardependencies' => true,
                'state' => $this->getState(),
                'globalreplacements' => $greplace,
                'replacements' => $replaces,
            ), $options));
        $this->setDate(date('Y-m-d')); // to avoid failed validation
        $maintainers = $this->getMaintainers();
        if ($maintainers) {
            foreach ($maintainers as $maintainer) {
                $pf->addMaintainer($maintainer['handle'], $maintainer['role'], $maintainer['name'],
                    $maintainer['email']);
            }
        }
        if (!isset($options['deps'])) {
            $deps = $this->getDeps(false, true);
            if ($deps) {
                foreach ($deps as $dep) {
                    $pf->addDependency(@$dep['name'], @$dep['version'], $dep['rel'], $dep['type'],
                        $dep['optional'] == 'yes');
                }
            }
        }
        return $pf;
    }

    /**
     * @param string
     * @param array
     */
    function &importFromPackageFile1($packagefile, $options = array())
    {
        $z = &PEAR_Config::singleton();
        $pkg = new PEAR_PackageFile($z);
        $pf = $pkg->fromPackageFile($packagefile, PEAR_VALIDATE_NORMAL);
        if (PEAR::isError($pf)) {
            return $pf;
        }
        if ($pf->getPackagexmlVersion() == '1.0') {
            $packagefile = &$pf;
        }
        return PEAR_PackageFile2Manager::importOptions($packagefile, $options);
    }

    /**
     * Import options from an existing package.xml
     *
     * @return PEAR_PackageFile2Manager|PEAR_Error
     * @static
     */
    function &importOptions($packagefile, $options = array())
    {
        if (is_a($packagefile, 'PEAR_PackageFile_v1')) {
            $gen = &$packagefile->getDefaultGenerator();
            $res = $gen->toV2('PEAR_PackageFile2Manager');
            $packagefile = $packagefile->getPackageFile();
        }
        if (PEAR::isError($res = 
              &PEAR_PackageFile2Manager::_getExistingPackageXML(dirname($packagefile) .
              DIRECTORY_SEPARATOR, basename($packagefile)))) {
            return $res;
        }
        if (PEAR::isError($ret = $res->_importOptions($packagefile, $options))) {
            return $ret;
        }
        return $res;
    }

    /**
     * @param string
     * @param array
     * @access private
     */
    function _importOptions($packagefile, $options)
    {
        $this->_options['packagedirectory'] = dirname($packagefile);
        $this->_options['pathtopackagefile'] = dirname($packagefile);
        $this->_options['baseinstalldir'] = '';
        return $this->setOptions(array_merge($this->_options, $options), true);
    }

    /**
     * Get the existing options
     * @return array
     */
    function getOptions()
    {
        return $this->_options;
    }

    /**
     * Add an extension/role mapping to the role mapping option
     *
     * Roles influence both where a file is installed and how it is installed.
     * Files with role="data" are in a completely different directory hierarchy
     * from the program files of role="php"
     * 
     * In PEAR 1.3b2, these roles are
     * - php (most common)
     * - data
     * - doc
     * - test
     * - script (gives the file an executable attribute)
     * - src
     * @param string file extension
     * @param string role
     * @throws PEAR_PACKAGEFILEMANAGER_INVALID_ROLE
     */
    function addRole($extension, $role)
    {
        require_once 'PEAR/Installer/Role.php';
        $roles = PEAR_Installer_Role::getValidRoles($this->getPackageType());
        if (!in_array($role, $roles)) {
            return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_INVALID_ROLE, implode($roles, ', '), $role);
        }
        $this->_options['roles'][$extension] = $role;
    }

    /**
     * Add a replacement option for all files
     *
     * This sets an install-time complex search-and-replace function
     * allowing the setting of platform-specific variables in all
     * installed files.
     *
     * if $type is php-const, then $to must be the name of a PHP Constant.
     * If $type is pear-config, then $to must be the name of a PEAR config
     * variable accessible through a {@link PEAR_Config::get()} method.  If
     * type is package-info, then $to must be the name of a section from
     * the package.xml file used to install this file.
     * @param string relative path of file (relative to packagedirectory option)
     * @param string variable type, either php-const, pear-config or package-info
     * @param string text to replace in the source file
     * @param string variable name to use for replacement
     * @throws PEAR_PACKAGEFILEMANAGER_INVALID_REPLACETYPE
     */
    function addGlobalReplacement($type, $from, $to)
    {
        require_once 'PEAR/Task/Replace/rw.php';
        if (!isset($this->_options['globalreplacements'])) {
            $this->_options['globalreplacements'] = array();
        }
        $l = null;
        $task = new PEAR_Task_Replace_rw($this, $this->_config, $l, '');
        $task->setInfo($from, $to, $type);
        if (is_array($res = $task->validate())) {
            return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_INVALID_REPLACETYPE,
                $res);
        }
        $this->_options['globalreplacements'][] = $task;
    }

    /**
     * Add a replacement option for a file
     *
     * This sets an install-time complex search-and-replace function
     * allowing the setting of platform-specific variables in an
     * installed file.
     *
     * if $type is php-const, then $to must be the name of a PHP Constant.
     * If $type is pear-config, then $to must be the name of a PEAR config
     * variable accessible through a {@link PEAR_Config::get()} method.  If
     * type is package-info, then $to must be the name of a section from
     * the package.xml file used to install this file.
     * @param string relative path of file (relative to packagedirectory option)
     * @param string variable type, either php-const, pear-config or package-info
     * @param string text to replace in the source file
     * @param string variable name to use for replacement
     */
    function addReplacement($path, $type, $from, $to)
    {
        if (!isset($this->_options['replacements'])) {
            $this->_options['replacements'] = array();
        }
        require_once 'PEAR/Task/Replace/rw.php';
        $l = null;
        $task = new PEAR_Task_Replace_rw($this, $this->_config, $l, '');
        $task->setInfo($from, $to, $type);
        if (is_array($res = $task->validate())) {
            return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_INVALID_REPLACETYPE,
                $res);
        }
        $this->_options['replacements'][$path][] = $task;
    }

    /**
     * @return void|PEAR_Error
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     */
    function detectDependencies()
    {
        if (!$this->_packageXml) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS);
        }
        if (!$this->isIncludeable('PHP/CompatInfo.php')) {
            return $this->raiseError(PEAR_PACKAGEFILEMANAGER_PHP_COMPAT_NOT_INSTALLED);
        } else {
            if (include_once('PHP/CompatInfo.php')) {
                $this->_detectDependencies = true;
            } else {
                $this->raiseError(PEAR_PACKAGEFILEMANAGER_NO_PHPCOMPATINFO);
            }
        }
    }

    function isIncludeable($file)
    {
        if (!defined('PATH_SEPARATOR')) {
            define('PATH_SEPARATOR', strtolower(substr(PHP_OS, 0, 3)) == 'win' ? ';' : ':');
        }
        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $file) &&
                  is_readable($path . DIRECTORY_SEPARATOR . $file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Writes the package.xml file out with the newly created <release></release> tag
     *
     * ALWAYS use {@link debugPackageFile} to verify that output is correct before
     * overwriting your package.xml
     * @param boolean null if no debugging, true if web interface, false if command-line
     * @throws PEAR_PACKAGEFILEMANAGER_RUN_SETOPTIONS
     * @throws PEAR_PACKAGEFILEMANAGER_ADD_MAINTAINERS
     * @throws PEAR_PACKAGEFILEMANAGER_CANTWRITE_PKGFILE
     * @throws PEAR_PACKAGEFILEMANAGER_CANTCOPY_PKGFILE
     * @throws PEAR_PACKAGEFILEMANAGER_CANTOPEN_TMPPKGFILE
     * @throws PEAR_PACKAGEFILEMANAGER_DEST_UNWRITABLE
     * @return void|PEAR_Error
     */
    function writePackageFile($debuginterface = null)
    {
        extract($this->_options);
        $warnings = $this->_stack->getErrors(true);
        $this->setDate(date('Y-m-d'));
        if (count($warnings)) {
            $nl = (isset($debuginterface) && $debuginterface ? '<br />' : "\n");
            foreach($warnings as $errmsg) {
                echo 'WARNING: ' . $errmsg['message'] . $nl;
            }
        }
        if ($this->_options['simpleoutput']) {
            $state = PEAR_VALIDATE_NORMAL;
        } else {
            $state = PEAR_VALIDATE_PACKAGING;
        }
        $this->_getDependencies();
        $this->_updateChangeLog();

        $outputdir = ($this->_options['outputdirectory'] ?
                        $this->_options['outputdirectory'] : $this->_options['packagedirectory']);
        $this->setPackagefile($outputdir . $this->_options['packagefile']);
        if (!$this->validate($state)) {
            $errors = $this->getValidationWarnings();
            $ret = '';
            $nl = (isset($debuginterface) && $debuginterface ? '<br />' : "\n");
            $haserror = false;
            foreach($errors as $err) {
                if (!$haserror && $err['level'] == 'error') {
                    $haserror = true;
                }
                if (isset($debuginterface) && $debuginterface) {
                    $msg = htmlspecialchars($err['message']);
                } else {
                    $msg = $err['message'];
                }
                $ret .= $err['level'] . ': ' . $msg . $nl;
            }
            if ($haserror) {
                return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_INVALID_PACKAGE, $nl, $ret);
            }
        }
        $gen = &$this->getDefaultGenerator();
        $packagexml = $gen->toXml($state);
        if (isset($debuginterface)) {
            if ($debuginterface) {
                echo '<pre>' . htmlentities($packagexml) . '</pre>';
            } else {
                echo $packagexml;
            }
            return true;
        }
        if ((file_exists($outputdir . $this->_options['packagefile']) &&
                is_writable($outputdir . $this->_options['packagefile']))
                ||
                @touch($outputdir . $this->_options['packagefile'])) {
            if ($fp = @fopen($outputdir . $this->_options['packagefile'] . '.tmp', "w")) {
                $written = @fwrite($fp, $packagexml);
                @fclose($fp);
                if ($written === false) {
                    return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_CANTWRITE_PKGFILE);
                }
                if (!@copy($outputdir . $this->_options['packagefile'] . '.tmp',
                        $outputdir . $this->_options['packagefile'])) {
                    return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_CANTCOPY_PKGFILE);
                } else {
                    @unlink($outputdir . $this->_options['packagefile'] . '.tmp');
                    return true;
                }
            } else {
                return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_CANTOPEN_TMPPKGFILE,
                    $outputdir . $this->_options['packagefile'] . '.tmp');
            }
        } else {
            return $this->raiseError(PEAR_PACKAGEFILE2MANAGER_DEST_UNWRITABLE, $outputdir);
        }
    }
    
    /**
     * ALWAYS use this to test output before overwriting your package.xml!!
     *
     * This method instructs writePackageFile() to simply print the package.xml
     * to output, either command-line or web-friendly (this is automatic
     * based on the existence of $_SERVER['PATH_TRANSLATED']
     * @uses writePackageFile() calls with the debug parameter set based on
     *       whether it is called from the command-line or web interface
     */
    function debugPackageFile()
    {
        $webinterface = (php_sapi_name() != 'cli');
        return $this->writePackageFile($webinterface);
    }
    
    /**
     * Store a warning on the warning stack
     */
    function pushWarning($code, $info)
    {
        $this->_warningStack[] = array('code' => $code,
                                       'message' => $this->_getMessage($code, $info));
    }
    
    /**
     * Retrieve the list of warnings
     * @return array
     */
    function getWarnings()
    {
        $a = $this->_warningStack;
        $this->_warningStack = array();
        return $a;
    }
    
    /**
     * Retrieve an error message from a code
     * @access private
     * @return string Error message
     */
    function _getMessage($code, $info)
    {
        $msg = $GLOBALS['_PEAR_PACKAGEFILE2MANAGER_ERRORS'][$this->_options['lang']][$code];
        foreach ($info as $name => $value) {
            $msg = str_replace('%' . $name . '%', $value, $msg);
        }
        return $msg;
    }
    
    /**
     * Utility function to shorten error generation code
     *
     * {@source}
     * @return PEAR_Error
     * @static
     */
    function raiseError($code, $i1 = '', $i2 = '')
    {
        return PEAR::raiseError('PEAR_PackageFile2Manager Error: ' .
                    sprintf($GLOBALS['_PEAR_PACKAGEFILE2MANAGER_ERRORS'][$this->_options['lang']][$code],
                    $i1, $i2), $code);
    }

    /**
     * @uses getDirTag() generate the xml from the array
     * @return string
     * @access private
     */
    function generateContents()
    {
        $options = $this->_options;
        if (count($this->_subpackages)) {
            if (!is_array($options['ignore'])) {
                $options['ignore'] = array();
            }
            for ($i = 0; $i < count($this->_subpackages); $i++) {
                $save = $this->_subpackages[$i]->getArray();
                $filelist = $this->_subpackages[$i]->getFileList();
                foreach ($filelist as $file => $atts) {
                    $options['ignore'][] = '*' . $file; // ignore all subpackage files
                }
                $this->_subpackages[$i]->fromArray($save);
            }
        }
        $generatorclass = 'PEAR_PackageFileManager_' . $this->_options['filelistgenerator'];
        $generator = new $generatorclass($this, $options);
        if ($this->_options['simpleoutput']) {
            $this->clearContents($this->_options['baseinstalldir']);
            return $this->_getSimpleDirTag($this->_struc = $generator->getFileList());
        }
        $this->clearContents($this->_options['baseinstalldir']);
        return $this->_getDirTag($this->_struc = $generator->getFileList()); 
    }

    /**
     * Recursively generate the <filelist> section's <dir> and <file> tags, but with
     * simple human-readable output
     * @param array|PEAR_Error the sorted directory structure, or an error
     *                         from filelist generation
     * @param false|string whether the parent directory has a role this should
     * inherit
     * @param integer indentation level
     * @return array|PEAR_Error
     * @access private
     */
    function _getSimpleDirTag($struc, $role = false, $_curdir = '')
    {
        if (PEAR::isError($struc)) {
            return $struc;
        }
        extract($this->_options);
        $ret = array();
    	foreach($struc as $dir => $files) {
    		if (false && $dir === '/') {
                // global directory role? overrides all exceptions except file exceptions
                if (isset($dir_roles['/'])) {
                    $role = $dir_roles['/'];
                }
                return $this->_getSimpleDirTag($struc[$dir], $role, '');
    		} else {
    		    // directory
    			if (!isset($files['file'])) {
    			    // contains only directories
                    if (isset($dir_roles[$_curdir . $dir])) {
                        $myrole = $dir_roles[$_curdir . $dir];
                    } else {
                        $myrole = $role;
                    }
                    $recurdir = ($_curdir == '') ? $dir . '/' : $_curdir . $dir . '/';
                    if ($recurdir == '//') {
                        $recurdir = '';
                    }
                    $this->_getSimpleDirTag($files, $myrole, $recurdir);
    			} else {
    			    // contains files
    				$myrole = '';
    				if (!$role)
    				{
    					$myrole = false;
    					if (isset($exceptions[$files['path']])) {
    						$myrole = $exceptions[$files['path']];
    					} elseif (isset($roles[$files['ext']])) {
    						$myrole = $roles[$files['ext']];
    					} else {
                            $myrole = $roles['*'];
                        }
    				} else {
                        $myrole = $role;
    					if (isset($exceptions[$files['path']])) {
    						$myrole = $exceptions[$files['path']];
    					}
                    }
                    $test = explode('/', $files['path']);
                    foreach ($test as $subpath) {
                        if ($subpath == 'CVS') {
                            $this->pushWarning(PEAR_PACKAGEFILE2MANAGER_CVS_PACKAGED,
                                array('path' => $files['path']));
                        }
                    }
    				$atts = array('role' => $myrole);
    				$diradd = dirname($files['path']);
                    $this->addFile($diradd == '.' ? '/' : $diradd, $files['file'], $atts);
                    if (isset($replacements[$files['path']])) {
                        foreach ($replacements[$files['path']] as $task) {
                            $this->addTaskToFile($files['path'], $task);
                        }
                    }
                    if (isset($globalreplacements)) {
                        foreach ($globalreplacements as $task) {
                            $this->addTaskToFile($files['path'], $task);
                        }
                    }
    			}
    		}
    	}
    	return;
    }
    
    /**
     * Recursively generate the <filelist> section's <dir> and <file> tags
     * @param array|PEAR_Error the sorted directory structure, or an error
     *                         from filelist generation
     * @param false|string whether the parent directory has a role this should
     * inherit
     * @param integer indentation level
     * @return array|PEAR_Error
     * @access private
     */
    function _getDirTag($struc, $role=false, $_curdir = '')
    {
        if (PEAR::isError($struc)) {
            return $struc;
        }
        extract($this->_options);
    	foreach($struc as $dir => $files) {
    		if ($dir === '/') {
                // global directory role? overrides all exceptions except file exceptions
                if (isset($dir_roles['/'])) {
                    $role = $dir_roles['/'];
                }
                return $this->_getDirTag($struc[$dir], $role, '');
    		} else {
    		    // non-global directory
    			if (!isset($files['file'])) {
    			    // contains only other directories
    				$myrole = '';
                    if (isset($dir_roles[$_curdir . $dir])) {
                        $myrole = $dir_roles[$_curdir . $dir];
                    } elseif ($role) {
                        $myrole = $role;
                    }
                    $this->_getDirTag($files, $myrole, $_curdir . $dir . '/');
    			} else {
    			    // contains files
    				$myrole = '';
    				if (!$role) {
    					$myrole = false;
    					if (isset($exceptions[$files['path']])) {
    						$myrole = $exceptions[$files['path']];
    					} elseif (isset($roles[$files['ext']])) {
    						$myrole = $roles[$files['ext']];
    					} else {
                            $myrole = $roles['*'];
                        }
    				} else {
                        $myrole = $role;
    					if (isset($exceptions[$files['path']])) {
    						$myrole = $exceptions[$files['path']];
    					}
                    }
                    if (isset($installexceptions[$files['path']])) {
                        $bi = $installexceptions[$files['path']];
                    } else {
                        $bi = $this->_options['baseinstalldir'];
                    }
                    $test = explode('/', $files['path']);
                    foreach ($test as $subpath) {
                        if ($subpath == 'CVS') {
                            $this->pushWarning(PEAR_PACKAGEFILE2MANAGER_CVS_PACKAGED,
                                array('path' => $files['path']));
                        }
                    }
    				$atts =
                        array('role' => $myrole,
                              'baseinstalldir' => $bi,
                              );
                    if (!isset($this->_options['simpleoutput']) || !$this->_options['simpleoutput']) {
                        $md5sum = @md5_file($this->_options['packagedirectory'] . $files['path']);
                        if (!empty($md5sum)) {
                            $atts['md5sum'] = $md5sum;
                        }
                    }
    				$diradd = dirname($files['path']);
                    $this->addFile($diradd == '.' ? '/' : $diradd, $files['file'], $atts);
                    if (isset($replacements[$files['path']])) {
                        foreach ($replacements[$files['path']] as $task) {
                            $this->addTaskToFile($files['path'], $task);
                        }
                    }
                    if (isset($globalreplacements)) {
                        foreach ($globalreplacements as $task) {
                            $this->addTaskToFile($files['path'], $task);
                        }
                    }
    			}
    		}
    	}
    	return;
    }

    /**
     * @param array
     * @access private
     */
    function _traverseFileArray($files, &$ret) {
        foreach ($files as $file) {
            if (!isset($file['fullpath'])) {
                $this->_traverseFileArray($file, $ret);
            } else {
                $ret[] = $file['fullpath'];
            }
        }
    }

    /**
     * Retrieve the 'deps' option passed to the constructor
     * @access private
     * @return array
     */
    function _getDependencies()
    {
        if ($this->_detectDependencies) {
            $this->_traverseFileArray($this->_struc, $ret);
            $compatinfo = new PHP_CompatInfo();
            $info = $compatinfo->parseArray($ret);
            $ret = $this->setPhpDep($info['version']);
            if (is_a($ret, 'PEAR_Error')) {
                return $ret;
            }
            foreach ($info['extensions'] as $ext) {
                $this->addExtensionDep('required', $ext);
            }
        }
        return;
    }

    /**
     * Creates a changelog entry with the current release
     * notes and dates, or overwrites a previous creation
     * @access private
     */
    function _updateChangeLog()
    {
        if ($this->_old) {
            $changelog = $this->_old->getChangelog();
        } else {
            $changelog = false;
        }
        $notes = $this->_options['changelognotes'];
        if (!$changelog) {
            $this->setChangelogEntry($this->getVersion(), $this->generateChangeLogEntry($notes));
            return;
        } else {
            if (!isset($changelog['release'][0])) {
                $changelog['release'] = array($changelog['release']);
            }
            $found = false;
            foreach ($changelog['release'] as $i => $centry) {
                $changelog['release'][$i]['notes'] = trim($changelog['release'][$i]['notes']);
                if ($centry['version']['release'] == $this->getVersion()) {
                    $changelog['release'][$i] = $this->generateChangeLogEntry($notes);
                    $found = true;
                }
            }
            if (!$found) {
                $changelog['release'][] = $this->generateChangeLogEntry($notes);
            }
            usort($changelog['release'], array($this, '_changelogsort'));
            $this->clearChangeLog();
            foreach ($changelog['release'] as $entry) {
                $this->setChangelogEntry($entry['version']['release'], $entry);
            }
        }
    }
    
    /**
     * @static
     * @access private
     */
    function _changelogsort($a, $b)
    {
        if ($this->_options['changelogoldtonew']) {
            $c = strtotime($a['date']);
            $d = strtotime($b['date']);
            $v1 = $a['version']['release'];
            $v2 = $b['version']['release'];
         } else {
            $d = strtotime($a['date']);
            $c = strtotime($b['date']);
            $v2 = $a['version']['release'];
            $v1 = $b['version']['release'];
        }
        if ($c - $d > 0) {
            return 1;
        } elseif ($c - $d < 0) {
            return -1;
         }
        return version_compare($v1, $v2);
    }

    function setOld()
    {
        $this->_old = new PEAR_PackageFile_v2;
        $this->_old->fromArray($this->getArray());
    }

    /**
     * @return true|PEAR_Error
     * @uses _generateNewPackageXML() if no package.xml is found, it
     *       calls this to create a new one
     * @param string full path to package file
     * @param string name of package file
     * @throws PEAR_PACKAGEFILE2MANAGER_PATH_DOESNT_EXIST
     * @access private
     * @static
     */
    function &_getExistingPackageXML($path, $packagefile = 'package.xml')
    {
        if (is_string($path) && is_dir($path)) {
            $contents = false;
            if (file_exists($path . $packagefile)) {
                $contents = file_get_contents($path . $packagefile);
            }
            if (!$contents) {
                return PEAR_PackageFile2Manager::_generateNewPackageXML();
            } else {
                require_once 'PEAR/PackageFile/Parser/v2.php';
                $pkg = &new PEAR_PackageFile_Parser_v2;
                $z = &PEAR_Config::singleton();
                $pkg->setConfig($z);
                $pf = &$pkg->parse($contents, $path . $packagefile, false, 'PEAR_PackageFile2Manager');
                if (!$pf->validate(PEAR_VALIDATE_DOWNLOADING)) {
                    return $pf->raiseError(PEAR_PACKAGEFILE2MANAGER_INVALID_PACKAGE);
                }
                if (PEAR::isError($pf)) {
                    return $pf;
                }
                $pf->setOld();
                $pf->clearDeps();
                $pf->clearContents();
            }
            return $pf;
        } else {
            if (!is_string($path)) {
                $path = gettype($path);
            }
            require_once 'PEAR.php';
            return PEAR::raiseError('Path does not exist: ' . $path, PEAR_PACKAGEFILE2MANAGER_PATH_DOESNT_EXIST);
        }
    }
    
    /**
     * Create the structure for a new package.xml
     *
     * @uses $_packageXml emulates reading in a package.xml
     *       by using the package, summary and description
     *       options
     * @return PEAR_PackageFile2Manager
     * @access private
     * @static
     */
    function &_generateNewPackageXML()
    {
        $this->_old = false;
        $pf = &new PEAR_PackageFile2Manager;
        return $pf;
    }
}

if (!function_exists('file_get_contents')) {
/**
 * @ignore
 */
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