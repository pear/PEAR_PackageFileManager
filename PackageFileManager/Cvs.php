<?php
/**
 * @package PEAR_PackageFileManager
 */
/**
 * The PEAR_PackageFileManager_File class
 */
require_once 'PEAR/PackageFileManager/File.php';

/**
 * Generate a file list from a CVS checkout
 *
 * Note that this will <b>NOT</b> work on a
 * repository, only on a checked out CVS module
 * @package PEAR_PackageFileManager
 */
class PEAR_PackageFileManager_CVS extends PEAR_PackageFileManager_File {
    /**
     * Return a list of all files in the CVS repository
     *
     * This function is like {@link parent::dirList()} except
     * that instead of retrieving a regular filelist, it first
     * retrieves a listing of all the CVS/Entries files in
     * $directory and all of the subdirectories.  Then, it
     * reads the Entries file, and creates a listing of files
     * that are a part of the CVS repository.  No check is
     * made to see if they have been modified, but newly
     * added or removed files are ignored.
     * @return array list of files in a directory
     * @param string $directory full path to the directory you want the list of
     * @uses _recurDirList()
     * @uses _readCVSEntries()
     */
    function dirList($directory)
    {
        $entries = $this->_recurDirList($directory);
        if (!$entries) {
            return PEAR_PackageFileManager::raiseError(PEAR_PACKAGEFILEMANAGER_NOCVSENTRIES, $directory);
        }
        return $this->_readCVSEntries($entries);
    }
    
    /**
     * Pull all the CVS/Entries files out from the base
     * directory
     * @param string current directory to traverse
     * @access private
     */
    function _recurDirList($directory)
    {
        $ret = false;
        if (@is_dir($directory)) {
            $ret = array();
            $d = @dir($directory); // thanks to Jason E Sweat (jsweat@users.sourceforge.net) for fix
            while($d && $entry=$d->read()) {
                if ($entry{0} != '.') {
                    if (is_file($directory . '/' . $entry) && (strcmp($entry, 'Entries') == 0)) {
                        $ret[] = $directory . '/' . $entry;
                    }
                    if (is_dir($directory . '/' . $entry)) {
                        $tmp = $this->_recurDirList($directory . '/' . $entry);
                        if (is_array($tmp)) {
                            foreach($tmp as $ent) {
                                $ret[] = $ent;
                            }
                        }
                    }
                }
            }
            if ($d) {
                $d->close();
            }
        } else {
            return PEAR_PackageFileManager::raiseError(PEAR_PACKAGEFILEMANAGER_DIR_DOESNT_EXIST, $directory);
        }
        return $ret;
    }
    
    /**
     * Iterate over the CVS Entries files, and retrieve every
     * file in the repository
     * @uses _getCVSEntries()
     * @uses _isCVSFile()
     * @param array array of full paths to CVS/Entries files
     * @access private
     */
    function _readCVSEntries($entries)
    {
        $ret = array();
        foreach($entries as $cvsentry) {
            $directory = dirname(dirname($cvsentry));
            $d = $this->_getCVSEntries($cvsentry);
            if (!is_array($d)) {
                continue;
            }
            foreach($d as $entry) {
                if ($this->_isCVSFile($entry)) {
                    $ret[] = $directory . '/' . $this->_getCVSFileName($entry);
                }
            }
        }
        return $ret;
    }
    
    /**
     * Retrieve the filename from an entry
     *
     * This method assumes that the entry is a file,
     * use _isCVSFile() to verify before calling
     * @param string a line in a CVS/Entries file
     * @return string the filename (no path information)
     * @access private
     */
    function _getCVSFileName($cvsentry)
    {
        $stuff = explode('/', $cvsentry);
        array_shift($stuff);
        return array_shift($stuff);
    }
    
    /**
     * Retrieve the entries in a CVS/Entries file
     * @return array each line of the entries file, output of file()
     * @uses function file()
     * @param string full path to a CVS/Entries file
     * @access private
     */
    function _getCVSEntries($cvsentryfilename)
    {
        $cvsfile = @file($cvsentryfilename);
        if (is_array($cvsfile)) {
            return $cvsfile;
        } else {
            return false;
        }
    }
    
    /**
     * Check whether an entry is a file or a directory
     * @return boolean
     * @param string a line in a CVS/Entries file
     * @access private
     */
    function _isCVSFile($cvsentry)
    {
        // make sure we ignore entries that have either been removed or added, but not committed yet
        return $cvsentry{0} == '/' && !strpos($cvsentry, 'dummy timestamp');
    }
}
?>
