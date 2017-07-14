<?php namespace ZN;

use Config, Import, Errors, File, GeneralException, Regex, Folder, Route, Arrays, Http, Lang, URI, URL, IS;

class In
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
    // Project Mode
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $mode: publication, development, restoration
    // @param int    $report: -1
    //
    // @return void
    //
    //--------------------------------------------------------------------------------------------------
    public static function projectMode(String $mode, Int $report = -1)
    {
        //----------------------------------------------------------------------------------------------
        // Kullanılabilir Uygulama Seçenekleri
        //----------------------------------------------------------------------------------------------
        switch( strtolower($mode) )
        {
            //------------------------------------------------------------------------------------------
            // Publication Yayın Modu
            // Tüm hatalar kapalıdır.
            // Projenin tamamlanmasından sonra bu modun kullanılması önerilir.
            //------------------------------------------------------------------------------------------
            case 'publication' :
                error_reporting(0);
            break;
            //------------------------------------------------------------------------------------------

            //------------------------------------------------------------------------------------------
            // Restoration Onarım Modu
            // Hataların görünümü görecelidir.
            //------------------------------------------------------------------------------------------
            case 'restoration' :
            //------------------------------------------------------------------------------------------
            // Development Geliştirme Modu
            // Tüm hatalar açıktır.
            //------------------------------------------------------------------------------------------
            case 'development' :
                error_reporting($report);
            break;
            //------------------------------------------------------------------------------------------

            //------------------------------------------------------------------------------------------
            // Farklı bir kullanım hatası
            //------------------------------------------------------------------------------------------
            default: trace('Invalid Application Mode! Available Options: ["development"], ["restoration"] or ["publication"]');
            //------------------------------------------------------------------------------------------
        }
        //----------------------------------------------------------------------------------------------
    }

    //--------------------------------------------------------------------------------------------------
    // internalInvalidRequest() - ZN >= 4.3.5
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $type
    // @param bool   $bool
    //
    //--------------------------------------------------------------------------------------------------
    public static function invalidRequest(String $type, Bool $bool)
    {
        $invalidRequest = Config::get('Services', 'route')['requestMethods'];

        if( $requestMethods = $invalidRequest[$type] )
        {
            $requestMethods = Arrays::lowerKeys($requestMethods);

            if( ! empty($requestMethod = $requestMethods[CURRENT_CFURI] ?? NULL) )
            {
                if( Http::isRequestMethod(...(array) $requestMethod) === $bool )
                {
                    Route::redirectInvalidRequest();
                }
            }
        }
    }

    //--------------------------------------------------------------------------------------------------
    // internalDefaultProjectKey() - ZN >= 4.2.7
    //--------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public static function defaultProjectKey(String $fix = NULL) : String
    {
        return md5(URL::base() . $fix);
    }

    //--------------------------------------------------------------------------------------------------
    // internalBaseDir() - ZN >= 4.2.6
    //--------------------------------------------------------------------------------------------------
    //
    // @param int $index = 0
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public static function baseDir(Int $index = 0) : String
    {
        $newBaseDir = BASE_DIR;

        if( BASE_DIR !== '/' )
        {
            $baseDir = substr(BASE_DIR, 1, -1);

            if( $index < 0 )
            {
                $baseDir    = explode('/', $baseDir);
                $newBaseDir = '/';

                for( $i = 0; $i < count($baseDir) + $index; $i++ )
                {
                    $newBaseDir .= suffix($baseDir[$i]);
                }
            }
        }

        return $newBaseDir;
    }


    //--------------------------------------------------------------------------------------------------
    // internalRequestURI()
    //--------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public static function requestURI() : String
    {
        $requestUri = URI::active()
                    ? str_replace(DIRECTORY_INDEX.'/', '', URI::active())
                    : substr(server('currentPath'), 1);

        if( isset($requestUri[strlen($requestUri) - 1]) && $requestUri[strlen($requestUri) - 1] === '/' )
        {
                $requestUri = substr($requestUri, 0, -1);
        }

        if( defined('_CURRENT_PROJECT') )
        {
            $requestUri = self::cleanURIPrefix($requestUri, _CURRENT_PROJECT);
        }

        $requestUri = self::cleanInjection(self::routeURI($requestUri));

        // 5.0.3 -> Updated ------------------------------------------------------
        $currentLang = Lang::current();

        if( ! empty(Lang::current()) && strlen($segment = URI::segment(1)) === 2 )
        {
            $currentLang = $segment;
        }
        // -----------------------------------------------------------------------

        $requestUri = self::cleanURIPrefix($requestUri, Lang::current());

        return (string) $requestUri;
    }

    //--------------------------------------------------------------------------------------------------
    // internalCleanURIPrefix()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $uri
    // @param string $cleanData
    //
    //--------------------------------------------------------------------------------------------------
    public static function cleanURIPrefix(String $uri = NULL, String $cleanData = NULL) : String
    {
        $suffixData = suffix((string) $cleanData);

        if( ! empty($cleanData) && stripos($uri, $suffixData) === 0 )
        {
            $uri = substr($uri, strlen($suffixData));
        }

        return $uri;
    }

    //--------------------------------------------------------------------------------------------------
    // internalRouteAll()
    //--------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------
    protected static function routeAll()
    {
        $files = (array) Folder::files(ROUTES_DIR, 'php');

        if( ! empty($files)  )
        {
            foreach( $files as $file )
            {
                import(ROUTES_DIR . $file);
            }

            Route::all();
        }
    }

    //--------------------------------------------------------------------------------------------------
    // internalRouteURI()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $requestUri
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public static function routeURI(String $requestUri = NULL) : String
    {
        self::routeAll();

        $config = Config::get('Services', 'route');

        if( $config['openController'] )
        {
            $internalDir = NULL;

            if( defined('_CURRENT_PROJECT') )
            {
                $configAppdir = PROJECTS_CONFIG['directory']['others'];

                if( is_array($configAppdir) )
                {
                    $internalDir = ! empty($configAppdir[$requestUri]) ? $requestUri : _CURRENT_PROJECT;
                }
                else
                {
                    $internalDir = _CURRENT_PROJECT;
                }
            }

            if
            (
                $requestUri === DIRECTORY_INDEX ||
                $requestUri === Lang::get()     ||
                $requestUri === $internalDir    ||
                empty($requestUri)
            )
            {
                $requestUri = $config['openController'];
            }
        }

        $uriChange   = $config['changeUri'];
        $patternType = $config['patternType'];

        if( ! empty($uriChange) ) foreach( $uriChange as $key => $val )
        {
            if( $patternType === 'classic' )
            {
                $requestUri = preg_replace(presuffix($key).'xi', $val, $requestUri);
            }
            else
            {
                $requestUri = Regex::replace($key, $val, $requestUri, 'xi');
            }
        }

        return $requestUri;
    }

    //--------------------------------------------------------------------------------------------------
    // internalCleanInjection()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $string
    //
    // @return string
    //
    //--------------------------------------------------------------------------------------------------
    public static function cleanInjection(String $string = NULL) : String
    {
        $urlInjectionChangeChars = Config::get('IndividualStructures', 'security')['urlChangeChars'];

        if( ! empty($urlInjectionChangeChars) ) foreach( $urlInjectionChangeChars as $key => $val )
        {
            $string = preg_replace(presuffix($key).'xi', $val, $string);
        }

        return $string;
    }

    //--------------------------------------------------------------------------------------------------
    // internalBenchmarkReport()
    //--------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------
    public static function benchmarkReport($start, $finish)
    {
        if( Config::get('Project', 'benchmark') === true && REQUEST_URI !== NULL )
        {
            //----------------------------------------------------------------------------------------------
            // System Elapsed Time Calculating
            //----------------------------------------------------------------------------------------------
            $elapsedTime = $finish - $start;
            //----------------------------------------------------------------------------------------------

            //----------------------------------------------------------------------------------------------
            // Get Memory Usage
            //----------------------------------------------------------------------------------------------
            $memoryUsage = memory_get_usage();
            //----------------------------------------------------------------------------------------------

            //----------------------------------------------------------------------------------------------
            // Get Maximum Memory Usage
            //----------------------------------------------------------------------------------------------
            $maxMemoryUsage = memory_get_peak_usage();
            //----------------------------------------------------------------------------------------------

            //----------------------------------------------------------------------------------------------
            // Template Benchmark Performance Result Table
            //----------------------------------------------------------------------------------------------
            $benchmarkData =
            [
                'elapsedTime'    => $elapsedTime,
                'memoryUsage'    => $memoryUsage,
                'maxMemoryUsage' => $maxMemoryUsage
            ];

            $benchResult = Import::template('BenchmarkTable', $benchmarkData, true);
            //----------------------------------------------------------------------------------------------

            //----------------------------------------------------------------------------------------------
            // Get Benchmark Performance Result Table
            //----------------------------------------------------------------------------------------------
            echo $benchResult;

            report('Benchmarking Test Result', $benchResult, 'BenchmarkTestResults');
            //----------------------------------------------------------------------------------------------
        }
    }

    //--------------------------------------------------------------------------------------------------
    // internalStartingConfig()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $config
    //
    //--------------------------------------------------------------------------------------------------
    public static function startingConfig($config)
    {
        if( $destruct = Config::get('Starting', $config) )
        {
            if( is_string($destruct) )
            {
                self::startingController($destruct);
            }
            elseif( is_array($destruct) )
            {
                foreach( $destruct as $key => $val )
                {
                    if( is_numeric($key) )
                    {
                        self::startingController($val);
                    }
                    else
                    {
                        self::startingController($key, $val);
                    }
                }
            }
        }
    }

    //--------------------------------------------------------------------------------------------------
    // internalStartingController()
    //--------------------------------------------------------------------------------------------------
    //
    // @param string $startController
    // @param array  $param
    //
    //--------------------------------------------------------------------------------------------------
    public static function startingController(String $startController = NULL, Array $param = [])
    {
        $controllerEx = explode(':', $startController);

        $controllerPath  = ! empty($controllerEx[0]) ? $controllerEx[0] : '';
        $controllerFunc  = ! empty($controllerEx[1]) ? $controllerEx[1] : 'main';
        $controllerFile  = CONTROLLERS_DIR . suffix($controllerPath, '.php');
        $controllerClass = divide($controllerPath, '/', -1);

        if( is_file($controllerFile) )
        {
            if( ! class_exists($controllerClass, false) )
            {
                $controllerClass = PROJECT_CONTROLLER_NAMESPACE . $controllerClass;
            }

            import($controllerFile);

            if( ! is_callable([$controllerClass, $controllerFunc]) )
            {
                report('Error', lang('Error', 'callUserFuncArrayError', $controllerFunc), 'SystemCallUserFuncArrayError');

                die(Errors::message('Error', 'callUserFuncArrayError', $controllerFunc));
            }

            return uselib($controllerClass)->$controllerFunc(...$param);
        }
        else
        {
            return false;
        }
    }

    //--------------------------------------------------------------------------------------------------
    // internalCreateHtaccessFile()
    //--------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------
    public static function createHtaccessFile()
    {
        // Cache.php ayar dosyasından ayarlar çekiliyor.
        $htaccessSettings = Config::get('Htaccess');

        $config = $htaccessSettings['cache'];
        $eol    = EOL;
        $tab    = HT;

        //-----------------------GZIP-------------------------------------------------------------
        // mod_gzip = true ayarı yapılmışsa aşağıdaki kodları ekler.
        // Gzip ile ön bellekleme başlatılmış olur.
        if( $config['modGzip']['status'] === true )
        {
            $modGzip  = '<ifModule mod_gzip.c>' . $eol;
            $modGzip .= $tab.'mod_gzip_on Yes' . $eol;
            $modGzip .= $tab.'mod_gzip_dechunk Yes' . $eol;
            $modGzip .= $tab.'mod_gzip_item_include file .('.$config['modGzip']['includedFileExtension'].')$' . $eol;
            $modGzip .= $tab.'mod_gzip_item_include handler ^cgi-script$' . $eol;
            $modGzip .= $tab.'mod_gzip_item_include mime ^text/.*' . $eol;
            $modGzip .= $tab.'mod_gzip_item_include mime ^application/x-javascript.*' . $eol;
            $modGzip .= $tab.'mod_gzip_item_exclude mime ^image/.*' . $eol;
            $modGzip .= $tab.'mod_gzip_item_exclude rspheader ^Content-Encoding:.*gzip.*' . $eol;
            $modGzip .= '</ifModule>' . $eol . $eol;
        }
        else
        {
            $modGzip = '';
        }
        //-----------------------GZIP-------------------------------------------------------------

        //-----------------------EXPIRES----------------------------------------------------------
        // mod_expires = true ayarı yapılmışsa aşağıdaki kodları ekler.
        // Tarayıcı ile ön bellekleme başlatılmış olur.
        if( $config['modExpires']['status'] === true )
        {
            $exp = '';
            foreach($config['modExpires']['fileTypeTime'] as $type => $value)
            {
                $exp .= $tab.'ExpiresByType '.$type.' "access plus '.$value.' seconds"'.$eol;
            }

            $modExpires  = '<ifModule mod_expires.c>' . $eol;
            $modExpires .= $tab.'ExpiresActive On' . $eol;
            $modExpires .= $tab.'ExpiresDefault "access plus '.$config['modExpires']['defaultTime'].' seconds"' . $eol;
            $modExpires .= rtrim($exp, $eol) . $eol;
            $modExpires .= '</ifModule>' . $eol . $eol;
        }
        else
        {
            $modExpires = '';
        }
        //-----------------------EXPIRES----------------------------------------------------------

        //-----------------------HEADERS----------------------------------------------------------
        // mod_headers = true ayarı yapılmışsa aşağıdaki kodları ekler.
        // Header ile ön bellekleme başlatılmış olur.
        if( $config['modHeaders']['status'] === true )
        {
            $fmatch = '';
            foreach( $config['modHeaders']['fileExtensionTimeAccess'] as $type => $value )
            {
                $fmatch .= $tab.'<filesMatch "\.('.$type.')$">' . $eol;
                $fmatch .= $tab.$tab.'Header set Cache-Control "max-age='.$value['time'].', '.$value['access'].'"' . $eol;
                $fmatch .= $tab.'</filesMatch>'.$eol;
            }

            $modHeaders  = '<ifModule mod_headers.c>' . $eol;
            $modHeaders .= rtrim($fmatch, $eol) . $eol;
            $modHeaders .= '</ifModule>' . $eol;
        }
        else
        {
            $modHeaders = '';
        }
        //-----------------------HEADERS----------------------------------------------------------

        //-----------------------HEADER SET-------------------------------------------------------

        if( $htaccessSettings['headers']['status'] === true )
        {
            $headersIniSet  = "<ifModule mod_expires.c>".$eol;

            foreach( $htaccessSettings['headers']['settings'] as $val )
            {
                $headersIniSet .= $tab."$val".$eol;
            }

            $headersIniSet .= "</ifModule>".$eol.$eol;
        }
        else
        {
            $headersIniSet = '';
        }
        //-----------------------HEADER SET-------------------------------------------------------

        //-----------------------HTACCESS SET-----------------------------------------------------

        if( ! empty($htaccessSettings['settings']) )
        {
            $htaccessSettingsStr = '';

            foreach( $htaccessSettings['settings'] as $key => $val )
            {
                if( ! is_numeric($key) )
                {
                    if( is_array($val) )
                    {
                        $htaccessSettingsStr .= "<$key>".$eol;

                        foreach( $val as $k => $v)
                        {
                            if( ! is_numeric($k) )
                            {
                                $htaccessSettingsStr .= $tab."$k $v".$eol;
                            }
                            else
                            {
                                $htaccessSettingsStr .= $tab.$v.$eol;
                            }
                        }

                        $keyex = explode(" ", $key);
                        $htaccessSettingsStr .= "</$keyex[0]>".$eol.$eol;
                    }
                    else
                    {
                        $htaccessSettingsStr .= "$key $val".$eol;
                    }
                }
                else
                {
                    $htaccessSettingsStr .= $val.$eol;
                }
            }
        }
        else
        {
            $htaccessSettingsStr = '';
        }
        //-----------------------HTACCESS SET-----------------------------------------------------

        // Htaccess dosyasına eklenecek veriler birleştiriliyor...

        $htaccess  = '#----------------------------------------------------------------------------------------------------'.$eol;
        $htaccess .= '# This file automatically created and updated'.$eol;
        $htaccess .= '#----------------------------------------------------------------------------------------------------'.$eol.$eol;
        $htaccess .= $modGzip.$modExpires.$modHeaders.$headersIniSet.$htaccessSettingsStr;

        //-----------------------URI ZERONEED PHP----------------------------------------------------
        if( ! $htaccessSettings['uri']['directoryIndex'] )
        {
            $indexSuffix = $htaccessSettings['uri']['indexSuffix'];
            $flag        = ! empty($indexSuffix) ? 'QSA' : 'L';

            $htaccess .= "<IfModule mod_rewrite.c>".$eol;
            $htaccess .= $tab."RewriteEngine On".$eol;
            $htaccess .= $tab."RewriteBase /".$eol;
            $htaccess .= $tab."RewriteCond %{REQUEST_FILENAME} !-f".$eol;
            $htaccess .= $tab."RewriteCond %{REQUEST_FILENAME} !-d".$eol;
            $htaccess .= $tab.'RewriteRule ^(.*)$  '.$_SERVER['SCRIPT_NAME'].$indexSuffix.'/$1 ['.$flag.']'.$eol;
            $htaccess .= "</IfModule>".$eol.$eol;
        }
        //-----------------------URI ZERONEED PHP----------------------------------------------------

        //-----------------------ERROR REQUEST----------------------------------------------------
        $htaccess .= 'ErrorDocument 403 '.BASE_DIR.DIRECTORY_INDEX.$eol.$eol;
        //-----------------------ERROR REQUEST----------------------------------------------------

        //-----------------------DIRECTORY INDEX--------------------------------------------------
        $htaccess .= 'DirectoryIndex '.DIRECTORY_INDEX.$eol.$eol;
        //-----------------------DIRECTORY INDEX--------------------------------------------------

        if( ! empty($uploadSet['status']) )
        {
            $uploadSettings = $htaccessSettings['upload'];
        }
        else
        {
            $uploadSettings = [];
        }
        //-----------------------UPLOAD SETTINGS--------------------------------------------------

        //-----------------------SESSION SETTINGS-------------------------------------------------

        if( ! empty($htaccessSettings['session']['status']) )
        {
            $sessionSettings = $htaccessSettings['session']['settings'];
        }
        else
        {
            $sessionSettings = [];
        }
        //-----------------------SESSION SETTINGS-------------------------------------------------

        //-----------------------INI SETTINGS-----------------------------------------------------
        if( $htaccessSettings['ini']['status'] === true )
        {
            $iniSettings = $htaccessSettings['ini']['settings'];
        }
        else
        {
            $iniSettings = [];
        }
        //-----------------------INI SETTINGS-----------------------------------------------------

        // Ayarlar birleştiriliyor.
        $allSettings = array_merge($iniSettings, $uploadSettings, $sessionSettings);

        if( ! empty($allSettings) )
        {
            $sets = '';

            foreach( $allSettings as $k => $v )
            {
                if( $v !== '' )
                {
                    $sets .= $tab."php_value $k $v".$eol;
                }
            }

            if( ! empty($sets) )
            {
                $htaccess .= $eol."<IfModule mod_php5.c>".$eol;
                $htaccess .= $sets;
                $htaccess .= "</IfModule>";
            }
        }

        $htaccessTxt = '.htaccess';

        if( File::exists($htaccessTxt) )
        {
            $getContents = trim(File::read($htaccessTxt));
        }
        else
        {
            $getContents = '';
        }

        $htaccess .= '#----------------------------------------------------------------------------------------------------';
        $htaccess  = trim($htaccess);

        if( $htaccess === $getContents )
        {
            return false;
        }

        if( ! File::write($htaccessTxt, trim($htaccess)) )
        {
            throw new GeneralException('Error', 'fileNotWrite', $htaccessTxt);
        }
    }

    //--------------------------------------------------------------------------------------------------
    // internalCreateRobotsFile()
    //--------------------------------------------------------------------------------------------------
    //
    // @param void
    //
    //--------------------------------------------------------------------------------------------------
    public static function createRobotsFile()
    {
        $rules  = Config::get('Robots', 'rules');
        $robots = '';

        if( IS::array($rules) ) foreach( $rules as $key => $val )
        {
            if( ! is_numeric($key) ) // Single Use
            {
                switch( $key )
                {
                    case 'userAgent' :
                        $robots .= ! empty( $val ) ? 'User-agent: '.$val.EOL : '';
                    break;

                    case 'allow'    :
                    case 'disallow' :
                        if( ! empty($val) ) foreach( $val as $v )
                        {
                            $robots .= ucfirst($key).': '.$v.EOL;
                        }
                    break;
                }
            }
            else
            {
                if( IS::array($val) ) foreach( $val as $r => $v ) // Multi Use
                {
                    switch( $r )
                    {
                        case 'userAgent' :
                            $robots .= ! empty( $v ) ? 'User-agent: '.$v.EOL : '';
                        break;

                        case 'allow'    :
                        case 'disallow' :
                            if( ! empty($v) ) foreach( $v as $vr )
                            {
                                $robots .= ucfirst($r).': '.$vr.EOL;
                            }
                        break;
                    }
                }
            }
        }

        $robotTxt = 'robots.txt';

        // robots.txt dosyası varsa içeriği al yok ise içeriği boş geç
        if( File::exists($robotTxt) )
        {
            $getContents = File::read($robotTxt);
        }
        else
        {
            $getContents = '';
        }
        // robots.txt değişkenin tuttuğu değer ile dosya içeri eşitse tekrar oluşturma
        if( trim($robots) === trim($getContents) )
        {
            return false;
        }

        if( ! File::write($robotTxt, trim($robots)) )
        {
            throw new GeneralException('Error', 'fileNotWrite', $robotTxt);
        }
    }
}
