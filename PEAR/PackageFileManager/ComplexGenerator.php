<?php
/**
 * Class for XML output
 *
 * PHP versions 4 and 5
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2003-2015 The PEAR Group
 * @license   New BSD, Revised
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     File available since Release 1.5.3
 */

require_once 'PEAR/PackageFile/Generator/v1.php';

/**
 * Class for XML output
 *
 * @category  PEAR
 * @package   PEAR_PackageFileManager
 * @author    Greg Beaver <cellog@php.net>
 * @copyright 2003-2015 The PEAR Group
 * @license   New BSD, Revised
 * @version   Release: @PEAR-VER@
 * @link      http://pear.php.net/package/PEAR_PackageFileManager
 * @since     Class available since Release 1.5.3
 */
class PEAR_PackageFileManager_ComplexGenerator extends PEAR_PackageFile_Generator_v1
{
    var $_options;
    var $_provides;

    /**
     * remove a warning about missing parameters - don't delete this
     */
    function __construct()
    {
    }

    /**
     * @param array $opts list of generation options
     *
     * @return void
     */
    function setPackageFileManagerOptions($opts)
    {
        $this->_options = $opts;
    }

    /**
     * Return an XML document based on the package info (as returned
     * by the PEAR_Common::infoFrom* methods).
     *
     * @param array $pkginfo package info
     *
     * @return string XML data
     * @access public
     * @deprecated use a PEAR_PackageFile_v* object's generator instead
     */
    function xmlFromInfo($pkginfo)
    {
        include_once 'PEAR/PackageFile.php';
        include_once 'PEAR/Config.php';
        $config = &PEAR_Config::singleton();
        $packagefile = new PEAR_PackageFile($config);
        $pf = &$packagefile->fromArray($pkginfo);
        if (!$pf->validate(PEAR_VALIDATE_NORMAL)) {
            $errors = $pf->getValidationWarnings();
            return PEAR::raiseError('Invalid package.xml file', null, null, null, $errors);
        }
        if (isset($this->_provides)) {
            $pf->_buildProvidesArray($this->_provides);
        }
        parent::PEAR_PackageFile_Generator_v1($pf);
        $ret = $this->toXml();
        if (!$ret) {
            $errors = $pf->getValidationWarnings();
            return PEAR::raiseError('Invalid package.xml file', null, null, null, $errors);
        }
        return $ret;
    }

    public static function getFileRoles()
    {
        return PEAR_Common::getFileRoles();
    }

    public static function getReplacementTypes()
    {
        return PEAR_Common::getReplacementTypes();
    }

    /**
     * Validate XML package definition file.
     *
     * @param string $info       Filename of the package archive or of the
     *                           package definition file
     * @param array  &$errors    Array that will contain the errors
     * @param array  &$warnings  Array that will contain the warnings
     * @param string $dir_prefix (optional) directory where source files
     *                           may be found, or empty if they are not available
     *
     * @access public
     * @return boolean
     * @deprecated use the validation of PEAR_PackageFile objects
     */
    function validatePackageInfo($info, &$errors, &$warnings, $dir_prefix = '')
    {
        // validation is done in xmlFromInfo()
        return true;
    }

    function analyzeSourceCode($file)
    {
        $pear = new PEAR_Common;
        return $pear->analyzeSourceCode($file);
    }

    function buildProvidesArray($a)
    {
        $this->_provides = $a;
    }
}
?>
