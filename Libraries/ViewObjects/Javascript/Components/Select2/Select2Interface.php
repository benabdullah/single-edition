<?php namespace ZN\ViewObjects\Javascript\Components;

interface Select2Interface
{
    //--------------------------------------------------------------------------------------------------------
    //
    // Author     : Ozan UYKUN <ozanbote@gmail.com>
    // Site       : www.znframework.com
    // License    : The MIT License
    // Copyright  : (c) 2012-2016, znframework.com
    //
    //--------------------------------------------------------------------------------------------------------

    //--------------------------------------------------------------------------------------------------------
    // Generate
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string   $id   = 'select2'
    // @param callable $select2
    //
    //--------------------------------------------------------------------------------------------------------
    public function generate(String $id = 'select2', Callable $select2) : String;
}
