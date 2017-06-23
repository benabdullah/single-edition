<?php
//--------------------------------------------------------------------------------------------------
// ZERONEED PHP WEB FRAMEWORK
//--------------------------------------------------------------------------------------------------
//
// Author     : Ozan UYKUN <ozanbote@windowslive.com> | <ozanbote@gmail.com>
// Site       : www.znframework.com
// License    : The MIT License
// Copyright  : Copyright (c) 2012-2016, ZN Framework
//
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
// Start
//--------------------------------------------------------------------------------------------------
//
// Microtime
//
//--------------------------------------------------------------------------------------------------
$start = microtime();

//--------------------------------------------------------------------------------------------------
// DS
//--------------------------------------------------------------------------------------------------
//
// @return const DIRECTORY_SEPARATOR
//
//--------------------------------------------------------------------------------------------------
define('DS', DIRECTORY_SEPARATOR);

//--------------------------------------------------------------------------------------------------
// REAL_BASE_DIR
//--------------------------------------------------------------------------------------------------
//
// @return /
//
//--------------------------------------------------------------------------------------------------
define('REAL_BASE_DIR', realpath(__DIR__) . DS);

//--------------------------------------------------------------------------------------------------
// Current Working Dir
//--------------------------------------------------------------------------------------------------
//
// @return /
//
//--------------------------------------------------------------------------------------------------
chdir(REAL_BASE_DIR);

//--------------------------------------------------------------------------------------------------
// Require Kernel
//--------------------------------------------------------------------------------------------------
//
// All necessary function pats and codes for ZN
//
//--------------------------------------------------------------------------------------------------
require_once 'zerocore.php';
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
// Run Kernel
//--------------------------------------------------------------------------------------------------
if( REQUEST_URI !== NULL )
{
    ZN\Core\Kernel::start()::run()::end();
}
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
// Finish
//--------------------------------------------------------------------------------------------------
//
// Microtime
//
//--------------------------------------------------------------------------------------------------
$finish = microtime();

//--------------------------------------------------------------------------------------------------
// Benchmark Table
//--------------------------------------------------------------------------------------------------
//
// Benchmark
//
//--------------------------------------------------------------------------------------------------
ZN\In::benchmarkReport((float) $start, (float) $finish);
