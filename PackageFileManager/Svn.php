<?php
/**
 * The SVN list plugin generator for both PEAR_PackageFileManager,
 * and PEAR_PackageFileManager2 classes.
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   pear
 * @package    PEAR_PackageFileManager
 * @author     Arnaud Limbourg <arnaud@limbourg.com>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    CVS: $Id$
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      File available since Release 1.3.0
 */

require_once 'PEAR/PackageFileManager/File.php';

/**
 * Generate a file list from a Subversion checkout
 *
 * Largely based on the CVS class, modified to suit
 * subversion organization.
 *
 * Note that this will <b>NOT</b> work on a
 * repository, only on a checked out Subversion module
 *
 * @category   pear
 * @package    PEAR_PackageFileManager
 * @author     Arnaud Limbourg <arnaud@limbourg.com>
 * @copyright  2005-2006 The PHP Group
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: @PEAR-VER@
 * @link       http://pear.php.net/package/PEAR_PackageFileManager
 * @since      Class available since Release 1.3.0
 */

class PEAR_PackageFileManager_Svn extends PEAR_PackageFileManager_File
{
    function PEAR_PackageFileManager_Svn(&$parent, $options)
    {
        if (version_compare(phpversion(), '5.0.0', '>=')) {
            if (!function_exists('simplexml_load_string')) {
                die('PEAR_PackageFileManager_SVN Error: simplexml extension is required' .
                    ' in PHP 5+.  PHP 4 uses XML_Tree');
            }
        }
        parent::PEAR_PackageFileManager_File(&$parent, $options);
    }

    /**
     * Return a list of all files in the CVS repository
     *
     * This function is like {@link parent::dirList()} except
     * that instead of retrieving a regular filelist, it first
     * retrieves a listing of all the .svn/entries files in
     * $directory and all of the subdirectories.  Then, it
     * reads the entries file, and creates a listing of files
     * that are a part of the Subversion checkout.  No check is
     * made to see if they have been modified, but removed files
     * are ignored.
     *
     * @access protected
     * @return array list of files in a directory
     * @param  string $directory full path to the directory you want the list of
     * @uses   _recurDirList()
     * @uses   _readSVNEntries()
     */
    function dirList($directory)
    {
        static $in_recursion = false;
        if (!$in_recursion) {
            // include only .svn/entries files
            // since subversion keeps its data in a hidden
            // directory we must force PackageFileManager to
            // consider hidden directories.
            $this->_options['addhiddenfiles'] = true;
            $this->_setupIgnore(array('*/.svn/entries'), 0);
            $this->_setupIgnore(array(), 1);
            $in_recursion = true;
            $entries = parent::dirList($directory);
            $in_recursion = false;
        } else {
            return parent::dirList($directory);
        }
        if (!$entries || !is_array($entries)) {
            return $this->_parent->raiseError(PEAR_PACKAGEFILEMANAGER_NOSVNENTRIES, $directory);
        }
        return $this->_readSVNEntries($entries);
    }

    /**
     * Iterate over the SVN Entries files, and retrieve every
     * file in the repository
     *
     * @uses _getSVNEntries()
     * @param array array of full paths to .svn/entries files
     * @access private
     */
    function _readSVNEntries($entries)
    {
        $ret = array();
        $ignore = $this->_options['ignore'];
        // implicitly ignore packagefile
        $ignore[] = $this->_options['packagefile'];
        $include = $this->_options['include'];
        $this->ignore = array(false, false);
        $this->_setupIgnore($ignore, 1);
        $this->_setupIgnore($include, 0);
        foreach ($entries as $cvsentry) {
            $directory = @dirname(@dirname($cvsentry));
            if (!$directory) {
                continue;
            }
            $d = $this->_getSVNEntries($cvsentry);
            if (!is_array($d)) {
                continue;
            }
            foreach ($d as $entry) {
                if ($ignore) {
                    if ($this->_checkIgnore($entry,
                          $directory . '/' . $entry, 1)) {
                        continue;
                    }
                }
                if ($include) {
                    if ($this->_checkIgnore($entry,
                          $directory . '/' . $entry, 0)) {
                        continue;
                    }
                }
                $ret[] = $directory . '/' . $entry;
            }
        }
        return $ret;
    }

    /**
     * Retrieve the entries in a .svn/entries file
     *
     * Uses XML_Tree to parse the XML subversion file
     *
     * It keeps only files, excluding directories. It also
     * makes sure no deleted file in included.
     *
     * @return array  an array with full paths to files
     * @uses   PEAR::XML_Tree
     * @param  string full path to a .svn/entries file
     * @access private
     */
    function _getSVNEntries($svnentriesfilename)
    {
        if (function_exists('simplexml_load_string')) {
            // this breaks simplexml because "svn:" is an invalid namespace, so strip it
            $stuff = str_replace('xmlns="svn:"', '', file_get_contents($svnentriesfilename));
            $all = simplexml_load_string($stuff);
            $entries = array();
            foreach ($all->entry as $entry) {
                if ($entry['kind'] == 'file') {
                    if (isset($entry['deleted'])) {
                        continue;
                    }
                    array_push($entries, $entry['name']);
                }
            }
        } else {
            require_once 'XML/Tree.php';
            $parser  = &new XML_Tree($svnentriesfilename);
            $tree    = &$parser->getTreeFromFile();

            // loop through the xml tree and keep only valid entries being files
            $entries = array();
            foreach ($tree->children as $entry) {
                if ($entry->name == 'entry'
                    && $entry->attributes['kind'] == 'file') {
                        if (isset($entry->attributes['deleted'])) {
                            continue;
                        }
                    array_push($entries, $entry->attributes['name']);
                }
            }

            unset($parser, $tree);
        }

        if (is_array($entries)) {
            return $entries;
        } else {
            return false;
        }
    }
}
?>