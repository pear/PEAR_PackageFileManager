<?php
//
// +------------------------------------------------------------------------+
// | PEAR :: Package File Manager                                           |
// +------------------------------------------------------------------------+
// | Copyright (c) 2004 Gregory Beaver                                      |
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
// $Id$
//

require_once 'PEAR/PackageFile/Generator/v1.php';
/**
 * Class for XML output
 *
 * @author Greg Beaver <cellog@php.net>
 * @since 1.2.0
 * @copyright 2003
 * @package PEAR_PackageFileManager
 */
class PEAR_PackageFileManager_SimpleGenerator extends PEAR_PackageFile_Generator_v1 {

    function PEAR_PackageFileManager_SimpleGenerator()
    {
    }

    /**
     * Return an XML document based on the package info (as returned
     * by the PEAR_Common::infoFrom* methods).
     *
     * @param array  $pkginfo  package info
     *
     * @return string XML data
     *
     * @access public
     * @deprecated use a PEAR_PackageFile_v* object's generator instead
     */
    function xmlFromInfo($pkginfo)
    {
        include_once 'PEAR/PackageFile.php';
        include_once 'PEAR/Config.php';
        $config = &PEAR_Config::singleton();
        $packagefile = &new PEAR_PackageFile($config);
        $pf = &$packagefile->fromArray($pkginfo);
        parent::PEAR_PackageFile_Generator_v1($pf);
        return $this->toXml();
    }

    function getFileRoles()
    {
        return PEAR_Common::getFileRoles();
    }

    /**
     * Validate XML package definition file.
     *
     * @param  string $info Filename of the package archive or of the
     *                package definition file
     * @param  array $errors Array that will contain the errors
     * @param  array $warnings Array that will contain the warnings
     * @param  string $dir_prefix (optional) directory where source files
     *                may be found, or empty if they are not available
     * @access public
     * @return boolean
     * @deprecated use the validation of PEAR_PackageFile objects
     */
    function validatePackageInfo($info, &$errors, &$warnings, $dir_prefix = '')
    {
        include_once 'PEAR/PackageFile.php';
        include_once 'PEAR/Config.php';
        $config = &PEAR_Config::singleton();
        $packagefile = &new PEAR_PackageFile($config);
        PEAR::staticPushErrorHandling(PEAR_ERROR_RETURN);
        if (is_array($info)) {
            $pf = &$packagefile->fromArray($info);
            if (!$pf->validate(PEAR_VALIDATE_NORMAL)) {
                foreach ($pf->getValidationWarnings() as $err) {
                    if ($error['level'] == 'error') {
                        $errors[] = $error['message'];
                    } else {
                        $warnings[] = $error['message'];
                    }
                }
                return false;
            }
        } else {
            $pf = &$packagefile->fromAnyFile($info, PEAR_VALIDATE_NORMAL);
        }
        PEAR::staticPopErrorHandling();
        if (PEAR::isError($pf)) {
            $errs = $pf->getUserinfo();
            if (is_array($errs)) {
                foreach ($errs as $error) {
                    if ($error['level'] == 'error') {
                        $errors[] = $error['message'];
                    } else {
                        $warnings[] = $error['message'];
                    }
                }
            }
            return false;
        }
        return true;
    }

    /**
     * Generate part of an XML description with release information.
     *
     * @param array  $pkginfo    array with release information
     * @param bool   $changelog  whether the result will be in a changelog element
     *
     * @return string XML data
     *
     * @access private
     */
    function _makeReleaseXml($pkginfo, $changelog = false)
    {
        $indent = $changelog ? "  " : "";
        $ret = "$indent  <release>\n";
        if (!empty($pkginfo['version'])) {
            $ret .= "$indent    <version>$pkginfo[version]</version>\n";
        }
        if (!empty($pkginfo['release_date'])) {
            $ret .= "$indent    <date>$pkginfo[release_date]</date>\n";
        }
        if (!empty($pkginfo['release_license'])) {
            $ret .= "$indent    <license>$pkginfo[release_license]</license>\n";
        }
        if (!empty($pkginfo['release_state'])) {
            $ret .= "$indent    <state>$pkginfo[release_state]</state>\n";
        }
        if (!empty($pkginfo['release_notes'])) {
            $ret .= "$indent    <notes>".htmlspecialchars($pkginfo['release_notes'])."</notes>\n";
        }
        if (!empty($pkginfo['release_warnings'])) {
            $ret .= "$indent    <warnings>".htmlspecialchars($pkginfo['release_warnings'])."</warnings>\n";
        }
        if (isset($pkginfo['release_deps']) && sizeof($pkginfo['release_deps']) > 0) {
            $ret .= "$indent    <deps>\n";
            foreach ($pkginfo['release_deps'] as $dep) {
                $ret .= "$indent      <dep type=\"$dep[type]\" rel=\"$dep[rel]\"";
                if (isset($dep['version'])) {
                    $ret .= " version=\"$dep[version]\"";
                }
                if (isset($dep['optional'])) {
                    $ret .= " optional=\"$dep[optional]\"";
                }
                if (isset($dep['name'])) {
                    $ret .= ">$dep[name]</dep>\n";
                } else {
                    $ret .= "/>\n";
                }
            }
            $ret .= "$indent    </deps>\n";
        }
        if (isset($pkginfo['configure_options'])) {
            $ret .= "$indent    <configureoptions>\n";
            foreach ($pkginfo['configure_options'] as $c) {
                $ret .= "$indent      <configureoption name=\"".
                    htmlspecialchars($c['name']) . "\"";
                if (isset($c['default'])) {
                    $ret .= " default=\"" . htmlspecialchars($c['default']) . "\"";
                }
                $ret .= " prompt=\"" . htmlspecialchars($c['prompt']) . "\"";
                $ret .= "/>\n";
            }
            $ret .= "$indent    </configureoptions>\n";
        }
        if (isset($pkginfo['provides'])) {
            foreach ($pkginfo['provides'] as $key => $what) {
                $ret .= "$indent    <provides type=\"$what[type]\" ";
                $ret .= "name=\"$what[name]\" ";
                if (isset($what['extends'])) {
                    $ret .= "extends=\"$what[extends]\" ";
                }
                $ret .= "/>\n";
            }
        }
        if (isset($pkginfo['filelist'])) {
            $ret .= "$indent    <filelist>\n";
            $ret .= $this->_doFileList($indent, $pkginfo['filelist'], '/');
            $ret .= "$indent    </filelist>\n";
        }
        $ret .= "$indent  </release>\n";
        return $ret;
    }

    /**
     * Generate the <filelist> tag
     * @access private
     * @return string
     */
    function _doFileList($indent, $filelist, $curdir)
    {
        $ret = '';
        foreach ($filelist as $file => $fa) {
            if (isset($fa['##files'])) {
                $ret .= "$indent      <dir";
            } else {
                $ret .= "$indent      <file";
            }

            if (isset($fa['role'])) {
                $ret .= " role=\"$fa[role]\"";
            }
            if (isset($fa['baseinstalldir'])) {
                $ret .= ' baseinstalldir="' .
                    htmlspecialchars($fa['baseinstalldir']) . '"';
            }
            if (isset($fa['md5sum'])) {
                $ret .= " md5sum=\"$fa[md5sum]\"";
            }
            if (isset($fa['platform'])) {
                $ret .= " platform=\"$fa[platform]\"";
            }
            if (!empty($fa['install-as'])) {
                $ret .= ' install-as="' .
                    htmlspecialchars($fa['install-as']) . '"';
            }
            $ret .= ' name="' . htmlspecialchars($file) . '"';
            if (isset($fa['##files'])) {
                $ret .= ">\n";
                $recurdir = $curdir;
                if ($recurdir == '///') {
                    $recurdir = '';
                }
                $ret .= $this->_doFileList("$indent ", $fa['##files'], $recurdir . $file . '/');
                $displaydir = $curdir;
                if ($displaydir == '///' || $displaydir == '/') {
                    $displaydir = '';
                }
                $ret .= "$indent      </dir> <!-- $displaydir$file -->\n";
            } else {
                if (empty($fa['replacements'])) {
                    $ret .= "/>\n";
                } else {
                    $ret .= ">\n";
                    foreach ($fa['replacements'] as $r) {
                        $ret .= "$indent        <replace";
                        foreach ($r as $k => $v) {
                            $ret .= " $k=\"" . htmlspecialchars($v) .'"';
                        }
                        $ret .= "/>\n";
                    }
                    $ret .= "$indent      </file>\n";
                }
            }
        }
        return $ret;
    }
    
}

?>