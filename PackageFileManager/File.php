<?php
/**
 * Retrieve the files from a directory listing
 * @package PEAR_PackageFileManager
 */
/**
 * Retrieve the files from a directory listing
 *
 * This class is used to retrieve a raw directory
 * listing.  Use the {@link PEAR_PackageFileManager_CVS}
 * class to only retrieve the contents of a cvs
 * repository when generating the package.xml
 * @package PEAR_PackageFileManager
 */
class PEAR_PackageFileManager_File {
    /**
     * @var array
     * @access private
     */
    var $_options = 
            array(
                 );

    /**
     * @access private
     * @var PEAR_PackageFileManager
     */
    var $_parent;

    /**
     * @access private
     * @var array|false
     */
    var $_ignore = false;

    /**
     * Set up the File filelist generator
     *
     * 'ignore' and 'include' are the only options that this class uses.  See
     * {@link PEAR_PackageFileManager::setOptions()} for
     * more information and formatting of this option
     * @param PEAR_PackageFileManager
     * @param array
     */
    function PEAR_PackageFileManager_File(&$parent, $options)
    {
        $this->_parent = &$parent;
        $this->_options = array_merge($this->_options, $options);
    }
    
    /**
     * Generate the <filelist></filelist> section
     * of the package file.
     *
     * This function performs the backend generation of the array
     * containing all files in this package
     * @return array
     */
    function getFileList()
    {
        $package_directory = $this->_options['packagedirectory'];
        $ignore = $this->_options['ignore'];
        $include = $this->_options['include'];
        $this->ignore = array(false, false);
        $this->_setupIgnore($ignore, 1);
        $this->_setupIgnore($include, 0);
        $allfiles = $this->dirList(substr($package_directory, 0, strlen($package_directory) - 1));
        if (PEAR::isError($allfiles)) {
            return $allfiles;
        }
        if (!count($allfiles)) {
            return PEAR_PackageFileManager::raiseError(PEAR_PACKAGEFILEMANAGER_NO_FILES,
                substr($package_directory, 0, strlen($package_directory) - 1));
        }
        $struc = array();
        foreach($allfiles as $file) {
            if ($ignore) {
            	if ($this->_checkIgnore(basename($file), dirname($file), $ignore, 1)) {
//                    print 'Ignoring '.$file."<br>\n";
                    continue;
                }
            }
            if ($include) {
                if ($this->_checkIgnore(basename($file), dirname($file), $include, 0)) {
//                    print 'Including '.$file."<br\n";
                    continue;
                }
            }
        	$path = substr(dirname($file), strlen(str_replace(DIRECTORY_SEPARATOR, 
                                                              '/',
                                                              realpath($package_directory))) + 1);
        	if (!$path) {
                $path = '/';
            }
        	$ext = array_pop(explode('.', $file));
        	if (strlen($ext) == strlen($file)) {
                $ext = '';
            }
        	$struc[$path][] = array('file' => basename($file),
                                    'ext' => $ext,
                                    'path' => (($path == '/') ? basename($file) : $path . '/' . basename($file)),
                                    'fullpath' => $file);
        }
        if (!count($struc)) {
            $newig = '';
            foreach($this->_options['ignore'] as $ig) {
                if (!empty($newig)) {
                    $newig .= ', ';
                }
                $newig .= $ig;
            }
            return PEAR_PackageFileManager::raiseError(PEAR_PACKAGEFILEMANAGER_IGNORED_EVERYTHING,
                substr($package_directory, 0, strlen($package_directory) - 1), $newig);
        }
        uksort($struc,'strnatcasecmp');
        foreach($struc as $key => $ind) {
        	usort($ind, array($this, 'sortfiles'));
        	$struc[$key] = $ind;
        }

        $tempstruc = $struc;
        $struc = array('/' => $tempstruc['/']);
        $bv = 0;
        foreach($tempstruc as $key => $ind) {
        	$save = $key;
        	if ($key != '/')
        	{
                $struc['/'] = $this->_setupDirs($struc['/'], explode('/',$key), $tempstruc[$key]);
        	}
        }
        uksort($struc['/'], array($this, 'mystrucsort'));

        return $struc;
    }
    
    /**
     * Retrieve a listing of every file in $directory and
     * all subdirectories.
     *
     * The return format is an array of full paths to files
     * @access protected
     * @return array list of files in a directory
     * @param string $directory full path to the directory you want the list of
     */
    function dirList($directory)
    {
        $ret = false;
        if (@is_dir($directory)) {
            $ret = array();
            $d = @dir($directory); // thanks to Jason E Sweat (jsweat@users.sourceforge.net) for fix
            while($d && $entry=$d->read()) {
                if ($entry{0} != '.') {
                    if (is_file($directory . '/' . $entry)) {
                        $ret[] = $directory . '/' . $entry;
                    }
                    if (is_dir($directory . '/' . $entry)) {
                        $tmp = $this->dirList($directory . '/' . $entry);
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
     * Tell whether to ignore a file or a directory
     * allows * and ? wildcards
     *
     * @param    string  $file    just the file name of the file or directory,
     *                          in the case of directories this is the last dir
     * @param    string  $path    the full path
     * @param    1|0    $return  value to return if regexp matches.  Set this to
     *                            false to include only matches, true to exclude
     *                            all matches
     * @return   bool    true if $path should be ignored, false if it should not
     * @access private
     */
    function _checkIgnore($file, $path, $return = 1)
    {
        $path = realpath($path);
        if (is_array($this->ignore[$return])) {
            foreach($this->ignore[$return] as $match) {
                // match is an array if the ignore parameter was a /path/to/pattern
                if (is_array($match)) {
                    // check to see if the path matches with a path delimiter appended
                    preg_match('/^' . strtoupper($match[0]).'$/', strtoupper($path) . '/',$find);
                    if (!count($find)) {
                        // check to see if it matches without an appended path delimiter
                        preg_match('/^' . strtoupper($match[0]).'$/', strtoupper($path), $find);
                    }
                    if (count($find)) {
                        // check to see if the file matches the file portion of the regex string
                        preg_match('/^' . strtoupper($match[1]).'$/', strtoupper($file), $find);
                        if (count($find)) {
                            return $return;
                        }
                    }
                    // check to see if the full path matches the regex
                    preg_match('/^' . strtoupper($match[0]).'$/',
                               strtoupper($path . DIRECTORY_SEPARATOR . $file), $find);
                    if (count($find)) {
                        return $return;
                    }
                } else {
                    // ignore parameter was just a pattern with no path delimiters
                    // check it against the path
                    preg_match('/^' . strtoupper($match).'$/', strtoupper($path), $find);
                    if (count($find)) {
                        return $return;
                    }
                    // check it against the file only
                    preg_match('/^' . strtoupper($match).'$/', strtoupper($file), $find);
                    if (count($find)) {
                        return $return;
                    }
                }
            }
        }
        return !$return;
    }
    
    /**
     * Construct the {@link $ignore} array
     * @param array strings of files/paths/wildcards to ignore
     * @access private
     */
    function _setupIgnore($ignore, $index)
    {
        $ig = array();
        if (is_array($ignore)) {
            for($i=0; $i<count($ignore);$i++) {
                $ignore[$i] = strtr($ignore[$i], "\\", "/");
                $ignore[$i] = str_replace('//','/',$ignore[$i]);

                if (!empty($ignore[$i])) {
                    if (!is_numeric(strpos($ignore[$i], '/'))) {
                        $ig[] = $this->_getRegExpableSearchString($ignore[$i]);
                    } else {
                        if (basename($ignore[$i]) . '/' == $ignore[$i]) {
                            $ig[] = $this->_getRegExpableSearchString($ignore[$i]);
                        } else {
                            $ig[] = array($this->_getRegExpableSearchString($ignore[$i]),
                                      $this->_getRegExpableSearchString(basename($ignore[$i])));
                        }
                    }
                }
            }
            if (count($ig)) {
                $this->ignore[$index] = $ig;
            } else {
                $this->ignore[$index] = false;
            }
        } else $this->ignore[$index] = false;
    }
    
    /**
     * Converts $s into a string that can be used with preg_match
     * @param string $s string with wildcards ? and *
     * @return string converts * to .*, ? to ., etc.
     * @access private
     */
    function _getRegExpableSearchString($s)
    {
        $y = '\/';
        if (DIRECTORY_SEPARATOR == '\\') {
            $y = '\\\\';
        }
        $s = str_replace('/', DIRECTORY_SEPARATOR, $s);
        $x = strtr($s, array('?' => '.','*' => '.*','.' => '\\.','\\' => '\\\\','/' => '\\/',
                                '[' => '\\[',']' => '\\]','-' => '\\-'));
        if (strpos($s, DIRECTORY_SEPARATOR) !== false &&
              strrpos($s, DIRECTORY_SEPARATOR) === strlen($s) - 1) {
            $x = "(?:.*$y$x?.*|$x.*)";
        }
        return $x;
    }
    
    /**
     * Recursively move contents of $struc into associative array
     *
     * The contents of $struc have many indexes like 'dir/subdir/subdir2'.
     * This function converts them to
     * array('dir' => array('subdir' => array('subdir2')))
     * @param array struc is array('dir' => array of files in dir,
     *              'dir/subdir' => array of files in dir/subdir,...)
     * @param array array form of 'dir/subdir/subdir2' array('dir','subdir','subdir2')
     * @return array same as struc but with array('dir' =>
     *              array(file1,file2,'subdir' => array(file1,...)))
     * @access private
     */
    function _setupDirs($struc, $dir, $contents)
    {
        if (!count($dir)) {
            foreach($contents as $dir => $files) {
                if (is_string($dir)) {
                    if (strpos($dir, '/')) {
                        $test = true;
                        $a = $contents[$dir];
                        unset($contents[$dir]);
                        $b = explode('/', $dir);
                        $c = array_shift($b);
                        if (isset($contents[$c])) {
                            $contents[$c] = $this->_setDir($contents[$c], $this->_setupDirs(array(), $b, $a));
                        } else {
                            $contents[$c] = $this->_setupDirs(array(), $b, $a);
                        }
                    }
                }
            }
            return $contents;
        }
        $me = array_shift($dir);
        if (!isset($struc[$me])) {
            $struc[$me] = array();
        }
        $struc[$me] = $this->_setupDirs($struc[$me], $dir, $contents);
        return $struc;
    }

    
    /**
     * Recursively add all the subdirectories of $contents to $dir without erasing anything in
     * $dir
     * @param array
     * @param array
     * @return array processed $dir
     * @access private
     */
    function _setDir($dir, $contents)
    {
        while(list($one,$two) = each($contents)) {
            if (isset($dir[$one])) {
                $dir[$one] = $this->_setDir($dir[$one], $contents[$one]);
            } else {
                $dir[$one] = $two;
            }
        }
        return $dir;
    }

    
    /**#@+
     * Sorting functions for the file list
     * @param string
     * @param string
     * @access private
     */
    function sortfiles($a, $b)
    {
        return strnatcasecmp($a['file'],$b['file']);
    }
    
    function mystrucsort($a, $b)
    {
        if (is_numeric($a) && is_string($b)) return 1;
        if (is_numeric($b) && is_string($a)) return -1;
        if (is_numeric($a) && is_numeric($b))
        {
            if ($a > $b) return 1;
            if ($a < $b) return -1;
            if ($a == $b) return 0;
        }
        return strnatcasecmp($a,$b);
    }
    /**#@-*/
}
?>
