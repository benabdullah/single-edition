<?php namespace ZN\Services\Response;

interface InternalHTTPInterface
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
    // Host -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function host() : String;

    //--------------------------------------------------------------------------------------------------------
    // User Agent -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function userAgent() : String;

    //--------------------------------------------------------------------------------------------------------
    // Accept -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function accept() : String;

    //--------------------------------------------------------------------------------------------------------
    // Accept Language -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function language() : String;

    //--------------------------------------------------------------------------------------------------------
    // Accept Encoding -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function encoding() : String;

    //--------------------------------------------------------------------------------------------------------
    // Cookie -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function cookie() : String;

    //--------------------------------------------------------------------------------------------------------
    // Connection -> 4.3.5
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function connection() : String;

    //--------------------------------------------------------------------------------------------------------
    // Is Request Request -> 4.3.1
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function isRequestMethod(...$methods) : Bool;

    //--------------------------------------------------------------------------------------------------------
    // Is Ajax
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function isAjax() : Bool;

    //--------------------------------------------------------------------------------------------------------
    // Is Curl
    //--------------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function isCurl() : Bool;

    //--------------------------------------------------------------------------------------------------------
    // Browser Lang
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $default tr
    // @param void
    //
    //--------------------------------------------------------------------------------------------------------
    public function browserLang(String $default = 'en') : String;

    //--------------------------------------------------------------------------------------------------------
    // Code
    //--------------------------------------------------------------------------------------------------------
    //
    // @param numeric $code
    //
    //--------------------------------------------------------------------------------------------------------
    public function code($code = 200) : String;

    //--------------------------------------------------------------------------------------------------------
    // Message
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $message
    //
    //--------------------------------------------------------------------------------------------------------
    public function message(String $message) : String;

    //--------------------------------------------------------------------------------------------------------
    // Name
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $name
    //
    //--------------------------------------------------------------------------------------------------------
    public function name(String $name) : InternalHTTP;

    //--------------------------------------------------------------------------------------------------------
    // Value
    //--------------------------------------------------------------------------------------------------------
    //
    // @param mixed $value
    //
    //--------------------------------------------------------------------------------------------------------
    public function value($value) : InternalHTTP;

    //--------------------------------------------------------------------------------------------------------
    // Input
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $input
    //
    //--------------------------------------------------------------------------------------------------------
    public function input(String $input) : InternalHTTP;

    //--------------------------------------------------------------------------------------------------------
    // Select
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $name
    //
    //--------------------------------------------------------------------------------------------------------
    public function select(String $name = NULL);

    //--------------------------------------------------------------------------------------------------------
    // Insert
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $name
    // @param string $value
    //
    //--------------------------------------------------------------------------------------------------------
    public function insert(String $name = NULL, $value = NULL) : Bool;

    //--------------------------------------------------------------------------------------------------------
    // Delete
    //--------------------------------------------------------------------------------------------------------
    //
    // @param string $name
    //
    //--------------------------------------------------------------------------------------------------------
    public function delete(String $name = NULL) : Bool;
}