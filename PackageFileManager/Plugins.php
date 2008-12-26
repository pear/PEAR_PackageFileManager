<?php
/**#@+
 * Error Codes
 */
define('PEAR_PACKAGEFILEMANAGER_PLUGINS_NOCVSENTRIES', 12);
define('PEAR_PACKAGEFILEMANAGER_PLUGINS_DIR_DOESNT_EXIST', 13);
define('PEAR_PACKAGEFILEMANAGER_PLUGINS_NO_FILES', 20);
define('PEAR_PACKAGEFILEMANAGER_PLUGINS_IGNORED_EVERYTHING', 21);
define('PEAR_PACKAGEFILEMANAGER_PLUGINS_NOSVNENTRIES', 32);
/**#@-*/
/**
 * Error messages
 * @global array $GLOBALS['_PEAR_PACKAGEFILEMANAGER_PLUGINS_ERRORS']
 */
$GLOBALS['_PEAR_PACKAGEFILEMANAGER_PLUGINS_ERRORS'] =
array(
    PEAR_PACKAGEFILEMANAGER_PLUGINS_NOCVSENTRIES =>
        'Directory "%s" is not a CVS directory (it must have the CVS/Entries file)',
    PEAR_PACKAGEFILEMANAGER_PLUGINS_DIR_DOESNT_EXIST =>
        'Package source base directory "%s" doesn\'t exist or isn\'t a directory',
    PEAR_PACKAGEFILEMANAGER_PLUGINS_NO_FILES =>
        'No files found, check the path "%s"',
    PEAR_PACKAGEFILEMANAGER_PLUGINS_IGNORED_EVERYTHING =>
        'No files left, check the path "%s" and ignore option "%s"',
    PEAR_PACKAGEFILEMANAGER_PLUGINS_NOSVNENTRIES =>
        'Directory "%s" is not a SVN directory (it must have the .svn/entries file)',
);

class PEAR_PackageFileManager_Plugins
{
    /**
     * Utility function to shorten error generation code
     *
     * {@source}
     *
     * @param integer $code error code
     * @param string  $i1   (optional) additional specific error info #1
     * @param string  $i2   (optional) additional specific error info #2
     *
     * @return PEAR_Error
     * @static
     * @access public
     * @since  1.6.0a1
     */
    function raiseError($code, $i1 = '', $i2 = '')
    {
        return PEAR::raiseError('PEAR_PackageFileManager_Plugins Error: ' .
                    sprintf($GLOBALS['_PEAR_PACKAGEFILEMANAGER_PLUGINS_ERRORS'][$code],
                    $i1, $i2), $code);
    }
}