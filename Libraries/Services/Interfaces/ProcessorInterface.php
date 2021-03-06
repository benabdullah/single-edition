<?php namespace ZN\Services;

interface ProcessorInterface
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
    // Type -> 5.4.4[added]
    //--------------------------------------------------------------------------------------------------------
    //
    // @param  void
    // @return string
    //
    //--------------------------------------------------------------------------------------------------------
    public function type() : String;

    //--------------------------------------------------------------------------------------------------------
    // Exec
    //--------------------------------------------------------------------------------------------------------
    //
    // @param  string $command: empty
    //
    //--------------------------------------------------------------------------------------------------------
    public function exec($command);

    //--------------------------------------------------------------------------------------------------------
    // Driver
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function driver(String $driver) : Processor;

    //--------------------------------------------------------------------------------------------------------
    // Output
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function output() : Array;

    //--------------------------------------------------------------------------------------------------------
    // Return
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function return() : Int;

    //--------------------------------------------------------------------------------------------------------
    // String Command
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function stringCommand() : String;
}
