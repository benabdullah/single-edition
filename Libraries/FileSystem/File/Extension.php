<?php namespace ZN\FileSystem\File;

use File;

class Extension implements ExtensionInterface
{
    //--------------------------------------------------------------------------------------------------------
    //
    // Author     : Ozan UYKUN <ozanbote@gmail.com>
    // Site       : www.znframework.com
    // License    : The MIT License
    // Copyright  : (c) 2012-2016, znframework.com
    //
    //--------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------
    // extension()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $file
    // @param bool   $dote = false
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public function get(String $file, Bool $dote = false) : String
    {
        $dote = $dote === true ? '.' : '';

        return $dote . strtolower(File::pathInfo($file, "extension"));
    }

    //--------------------------------------------------------------------------------------------------
    // removeExtension()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $file
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public function remove(String $file) : String
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $file);
    }
}
