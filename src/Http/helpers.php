<?php
    use Illuminate\Support\Facades\File;
    use Seshra\Core\Core;
    use Illuminate\Support\Facades\Auth;
    use Seshra\Core\EventManager;
    use Seshra\Core\Helpers\Settings;
    use Seshra\Core\Models\Currency;

    if (! function_exists('core')) {
        function core()
        {
            return app()->make(Core::class);
        }
    }

    if (! function_exists('translate')) {
        function translate($key, $locale = null)
        {
            return core()->translate($key, $locale);
        }
    }

    if (! function_exists('lang')) {
        function lang($lang)
        {
            return ($lang != '') ? $lang : config('app.fallback_locale');
        }
    }

    if (! function_exists('locale')) {
        function locale()
        {
            if ($locale = session()->get('locale')) {
                return $locale;
            }
            return config('app.fallback_locale');
        }
    }

    if (! function_exists('locales')) {
        function locales()
        {
            return core()->getAllLocales();
        }
    }
    
    //highlights the selected navigation on admin panel
    if (! function_exists('are_active_routes')) {
        function are_active_routes(Array $routes, $output = "active")
        {
            foreach ($routes as $route) {
                if (Route::currentRouteName() == $route) return $output;
            }

        }
    }

    //overwrite .env file
    if (! function_exists('update_env_file')) {
        function update_env_file($type, $val)
        {
            $path = base_path('.env');
            if (file_exists($path)) {
                $val = '"'.trim($val).'"';
                if(is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0){
                    file_put_contents($path, str_replace(
                        $type.'="'.env($type).'"', $type.'='.$val, file_get_contents($path)
                    ));
                }
                else{
                    file_put_contents($path, file_get_contents($path)."\r\n".$type.'='.$val);
                }
            }
        }
    }

    if (! function_exists('show_category')) {
        function show_category($category, $parents, $type)
        {
            return in_array($category, $parents) ? $type : '';
        }
    }

    if (!function_exists('isHttps')) {
        function isHttps()
        {
            return !empty($_SERVER['HTTPS']) && ('on' == $_SERVER['HTTPS']);
        }
    }

    function timezones(){
        $timezones = Array(
            '(GMT-12:00) International Date Line West' => 'Pacific/Kwajalein',
            '(GMT-11:00) Midway Island' => 'Pacific/Midway',
            '(GMT-11:00) Samoa' => 'Pacific/Apia',
            '(GMT-10:00) Hawaii' => 'Pacific/Honolulu',
            '(GMT-09:00) Alaska' => 'America/Anchorage',
            '(GMT-08:00) Pacific Time (US & Canada)' => 'America/Los_Angeles',
            '(GMT-08:00) Tijuana' => 'America/Tijuana',
            '(GMT-07:00) Arizona' => 'America/Phoenix',
            '(GMT-07:00) Mountain Time (US & Canada)' => 'America/Denver',
            '(GMT-07:00) Chihuahua' => 'America/Chihuahua',
            '(GMT-07:00) La Paz' => 'America/Chihuahua',
            '(GMT-07:00) Mazatlan' => 'America/Mazatlan',
            '(GMT-06:00) Central Time (US & Canada)' => 'America/Chicago',
            '(GMT-06:00) Central America' => 'America/Managua',
            '(GMT-06:00) Guadalajara' => 'America/Mexico_City',
            '(GMT-06:00) Mexico City' => 'America/Mexico_City',
            '(GMT-06:00) Monterrey' => 'America/Monterrey',
            '(GMT-06:00) Saskatchewan' => 'America/Regina',
            '(GMT-05:00) Eastern Time (US & Canada)' => 'America/New_York',
            '(GMT-05:00) Indiana (East)' => 'America/Indiana/Indianapolis',
            '(GMT-05:00) Bogota' => 'America/Bogota',
            '(GMT-05:00) Lima' => 'America/Lima',
            '(GMT-05:00) Quito' => 'America/Bogota',
            '(GMT-04:00) Atlantic Time (Canada)' => 'America/Halifax',
            '(GMT-04:00) Caracas' => 'America/Caracas',
            '(GMT-04:00) La Paz' => 'America/La_Paz',
            '(GMT-04:00) Santiago' => 'America/Santiago',
            '(GMT-03:30) Newfoundland' => 'America/St_Johns',
            '(GMT-03:00) Brasilia' => 'America/Sao_Paulo',
            '(GMT-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
            '(GMT-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
            '(GMT-03:00) Greenland' => 'America/Godthab',
            '(GMT-02:00) Mid-Atlantic' => 'America/Noronha',
            '(GMT-01:00) Azores' => 'Atlantic/Azores',
            '(GMT-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
            '(GMT) Casablanca' => 'Africa/Casablanca',
            '(GMT) Dublin' => 'Europe/London',
            '(GMT) Edinburgh' => 'Europe/London',
            '(GMT) Lisbon' => 'Europe/Lisbon',
            '(GMT) London' => 'Europe/London',
            '(GMT) UTC' => 'UTC',
            '(GMT) Monrovia' => 'Africa/Monrovia',
            '(GMT+01:00) Amsterdam' => 'Europe/Amsterdam',
            '(GMT+01:00) Belgrade' => 'Europe/Belgrade',
            '(GMT+01:00) Berlin' => 'Europe/Berlin',
            '(GMT+01:00) Bern' => 'Europe/Berlin',
            '(GMT+01:00) Bratislava' => 'Europe/Bratislava',
            '(GMT+01:00) Brussels' => 'Europe/Brussels',
            '(GMT+01:00) Budapest' => 'Europe/Budapest',
            '(GMT+01:00) Copenhagen' => 'Europe/Copenhagen',
            '(GMT+01:00) Ljubljana' => 'Europe/Ljubljana',
            '(GMT+01:00) Madrid' => 'Europe/Madrid',
            '(GMT+01:00) Paris' => 'Europe/Paris',
            '(GMT+01:00) Prague' => 'Europe/Prague',
            '(GMT+01:00) Rome' => 'Europe/Rome',
            '(GMT+01:00) Sarajevo' => 'Europe/Sarajevo',
            '(GMT+01:00) Skopje' => 'Europe/Skopje',
            '(GMT+01:00) Stockholm' => 'Europe/Stockholm',
            '(GMT+01:00) Vienna' => 'Europe/Vienna',
            '(GMT+01:00) Warsaw' => 'Europe/Warsaw',
            '(GMT+01:00) West Central Africa' => 'Africa/Lagos',
            '(GMT+01:00) Zagreb' => 'Europe/Zagreb',
            '(GMT+02:00) Athens' => 'Europe/Athens',
            '(GMT+02:00) Bucharest' => 'Europe/Bucharest',
            '(GMT+02:00) Cairo' => 'Africa/Cairo',
            '(GMT+02:00) Harare' => 'Africa/Harare',
            '(GMT+02:00) Helsinki' => 'Europe/Helsinki',
            '(GMT+02:00) Istanbul' => 'Europe/Istanbul',
            '(GMT+02:00) Jerusalem' => 'Asia/Jerusalem',
            '(GMT+02:00) Kyev' => 'Europe/Kiev',
            '(GMT+02:00) Minsk' => 'Europe/Minsk',
            '(GMT+02:00) Pretoria' => 'Africa/Johannesburg',
            '(GMT+02:00) Riga' => 'Europe/Riga',
            '(GMT+02:00) Sofia' => 'Europe/Sofia',
            '(GMT+02:00) Tallinn' => 'Europe/Tallinn',
            '(GMT+02:00) Vilnius' => 'Europe/Vilnius',
            '(GMT+03:00) Baghdad' => 'Asia/Baghdad',
            '(GMT+03:00) Kuwait' => 'Asia/Kuwait',
            '(GMT+03:00) Moscow' => 'Europe/Moscow',
            '(GMT+03:00) Nairobi' => 'Africa/Nairobi',
            '(GMT+03:00) Riyadh' => 'Asia/Riyadh',
            '(GMT+03:00) St. Petersburg' => 'Europe/Moscow',
            '(GMT+03:00) Volgograd' => 'Europe/Volgograd',
            '(GMT+03:30) Tehran' => 'Asia/Tehran',
            '(GMT+04:00) Abu Dhabi' => 'Asia/Muscat',
            '(GMT+04:00) Baku' => 'Asia/Baku',
            '(GMT+04:00) Muscat' => 'Asia/Muscat',
            '(GMT+04:00) Tbilisi' => 'Asia/Tbilisi',
            '(GMT+04:00) Yerevan' => 'Asia/Yerevan',
            '(GMT+04:30) Kabul' => 'Asia/Kabul',
            '(GMT+05:00) Ekaterinburg' => 'Asia/Yekaterinburg',
            '(GMT+05:00) Islamabad' => 'Asia/Karachi',
            '(GMT+05:00) Karachi' => 'Asia/Karachi',
            '(GMT+05:00) Tashkent' => 'Asia/Tashkent',
            '(GMT+05:30) Chennai' => 'Asia/Kolkata',
            '(GMT+05:30) Kolkata' => 'Asia/Kolkata',
            '(GMT+05:30) Mumbai' => 'Asia/Kolkata',
            '(GMT+05:30) New Delhi' => 'Asia/Kolkata',
            '(GMT+05:45) Kathmandu' => 'Asia/Kathmandu',
            '(GMT+06:00) Almaty' => 'Asia/Almaty',
            '(GMT+06:00) Astana' => 'Asia/Dhaka',
            '(GMT+06:00) Dhaka' => 'Asia/Dhaka',
            '(GMT+06:00) Novosibirsk' => 'Asia/Novosibirsk',
            '(GMT+06:00) Sri Jayawardenepura' => 'Asia/Colombo',
            '(GMT+06:30) Rangoon' => 'Asia/Rangoon',
            '(GMT+07:00) Bangkok' => 'Asia/Bangkok',
            '(GMT+07:00) Hanoi' => 'Asia/Bangkok',
            '(GMT+07:00) Jakarta' => 'Asia/Jakarta',
            '(GMT+07:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
            '(GMT+08:00) Beijing' => 'Asia/Hong_Kong',
            '(GMT+08:00) Chongqing' => 'Asia/Chongqing',
            '(GMT+08:00) Hong Kong' => 'Asia/Hong_Kong',
            '(GMT+08:00) Irkutsk' => 'Asia/Irkutsk',
            '(GMT+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
            '(GMT+08:00) Perth' => 'Australia/Perth',
            '(GMT+08:00) Singapore' => 'Asia/Singapore',
            '(GMT+08:00) Taipei' => 'Asia/Taipei',
            '(GMT+08:00) Ulaan Bataar' => 'Asia/Irkutsk',
            '(GMT+08:00) Urumqi' => 'Asia/Urumqi',
            '(GMT+09:00) Osaka' => 'Asia/Tokyo',
            '(GMT+09:00) Sapporo' => 'Asia/Tokyo',
            '(GMT+09:00) Seoul' => 'Asia/Seoul',
            '(GMT+09:00) Tokyo' => 'Asia/Tokyo',
            '(GMT+09:00) Yakutsk' => 'Asia/Yakutsk',
            '(GMT+09:30) Adelaide' => 'Australia/Adelaide',
            '(GMT+09:30) Darwin' => 'Australia/Darwin',
            '(GMT+10:00) Brisbane' => 'Australia/Brisbane',
            '(GMT+10:00) Canberra' => 'Australia/Sydney',
            '(GMT+10:00) Guam' => 'Pacific/Guam',
            '(GMT+10:00) Hobart' => 'Australia/Hobart',
            '(GMT+10:00) Melbourne' => 'Australia/Melbourne',
            '(GMT+10:00) Port Moresby' => 'Pacific/Port_Moresby',
            '(GMT+10:00) Sydney' => 'Australia/Sydney',
            '(GMT+10:00) Vladivostok' => 'Asia/Vladivostok',
            '(GMT+11:00) Magadan' => 'Asia/Magadan',
            '(GMT+11:00) New Caledonia' => 'Asia/Magadan',
            '(GMT+11:00) Solomon Is.' => 'Asia/Magadan',
            '(GMT+12:00) Auckland' => 'Pacific/Auckland',
            '(GMT+12:00) Fiji' => 'Pacific/Fiji',
            '(GMT+12:00) Kamchatka' => 'Asia/Kamchatka',
            '(GMT+12:00) Marshall Is.' => 'Pacific/Fiji',
            '(GMT+12:00) Wellington' => 'Pacific/Auckland',
            '(GMT+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
        );
    
        return $timezones;
    }
    
    if (!function_exists('app_timezone')) {
        function app_timezone()
        {
            return config('app.timezone');
        }
    }

    //returns combinations of customer choice options array
    if (! function_exists('combinations')) {
        function combinations($arrays) {
            $result = array(array());
            foreach ($arrays as $property => $property_values) {
                $tmp = array();
                foreach ($result as $result_item) {
                    foreach ($property_values as $property_value) {
                        $tmp[] = array_merge($result_item, array($property => $property_value));
                    }
                }
                $result = $tmp;
            }
            return $result;
        }
    }

    if (! function_exists('combine_pivot')) {
        function combine_pivot($entities, $pivots = [])
        {
            // Set array
            $pivotArray = [];
            // Loop through all pivot attributes
            foreach ($pivots as $pivot => $value) {
                // Combine them to pivot array
                $pivotArray += [$pivot => $value];
            }
            // Get the total of arrays we need to fill
            $total = count($entities);
            // Make filler array
            $filler = array_fill(0, $total, $pivotArray);
            // Combine and return filler pivot array with data
            return array_combine($entities, $filler);
        }
    }

    if (!function_exists('scan_folder')) {
       
        function scan_folder($path, $ignore_files = [])
        {
            try {
                if (is_dir($path)) {
                    $data = array_diff(scandir($path), array_merge(['.', '..'], $ignore_files));
                    natsort($data);
                    return $data;
                }
                return [];
            } catch (Exception $ex) {
                return [];
            }
        }
    }

    if (!function_exists('get_file_data')) {
      
        function get_file_data($file, $convert_to_array = true)
        {
            
            $file = File::exists($file) ? File::get($file) : '';
            if (!empty($file)) {
                if ($convert_to_array) {
                    return json_decode($file, true);
                } else {
                    return $file;
                }
            }
            return false;
        }
    }

    if (!function_exists('current_guard')) {
        
        function current_guard()
        {
            $guards = array_keys(config('auth.guards'));
            return collect($guards)->first(function($guard){
                return auth()->guard($guard)->check();
            });
        }

    }

    if (!function_exists('current_user')) {
        
        function current_user()
        {
            $user = null;
            $guards = config('auth.guards');
            foreach($guards as $guard => $values) {
                if(Auth::guard($guard)->check()) {
                    $user = Auth::guard($guard)->user();
                }
            }
            return $user;
        }

    }

    if (!function_exists('settings')) {
        
        function settings($key, $default = null)
        {
            return Settings::get($key, $default);
        }

    }

    if (!function_exists('seshra')) {
        
        function seshra($key, $params = null)
        {
            switch($key) {
                case 'favicon_url':
                    return (setting('system_favicon') != '') ? media(setting('system_favicon')) : asset('assets/img/favicon.jpeg');
                    break;
                case 'logo_url':
                    $key = 'system_logo_'.$params;
                    return (setting($key) != '') ? media(setting($key)) : asset('assets/img/logo.png');
                    break;
                case 'system_name':
                    return (setting('site_name') != '') ? setting('site_name') : config('app.name');
                    break;
                case 'base_url':
                    $root = (isHttps() ? "https://" : "http://").$_SERVER['HTTP_HOST'];
                    $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
                    return $root;
                    break;
                case 'file_base_url':
                    if(env('FILESYSTEM_DRIVER') == 's3'){
                        return env('AWS_URL').'/';
                    }
                    else {
                        return seshra('bas_url').'public/';
                    }
                    break;
            }
        }

    }

    if (! function_exists('filter')) {
        function filter($eventName, $params = null)
        {
            app()->singleton(EventManager::class);
    
            $eventManager = app()->make(EventManager::class);
    
            $eventManager->handleRenderEvent($eventName, $params);
    
            return $eventManager->render();
        }
    }

    if (! function_exists('currency')) {
        function currency($amount)
        {
            $default_currency_id = setting('system_default_currency');
            $currency_symbol_format = setting('currency_symbol_format');
            $currency_decimal_separator = setting('currency_decimal_separator');
            $no_of_decimals = setting('currency_no_of_decimals');
            if(
                $default_currency_id != null && 
                $currency_symbol_format != null && 
                $currency_decimal_separator != null && 
                $no_of_decimals != null
            ) {
                $system_default_currency = Currency::findorfail($default_currency_id);
                $currency_symbol = $system_default_currency->symbol;
                $amount = ($currency_decimal_separator == 1 ) ? number_format($amount, $no_of_decimals, '.', ',') : number_format($amount, $no_of_decimals, ',', '.');
                return ($currency_symbol_format == 1) ? sprintf('%s %s', $currency_symbol, $amount) : sprintf('%s %s', $amount, $currency_symbol);
            }
            throw new Exception('Please set system currency configurations');
        }
    }