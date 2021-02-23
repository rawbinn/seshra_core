<?php

namespace Seshra\Core\Helpers;

use Setting;
use Illuminate\Support\Arr;

class Settings
{

    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $count = count($keys);
        $setting = Setting::get($keys[0], $default);
        $settings = json_decode($setting, true);
        if(is_array($settings)) {
            array_shift($keys);
            $skey = implode('.', $keys);
            return $count == 1 ? $settings :  Arr::get($settings, $skey);
        }

        return $setting;
    }

    /**
     * @return void
     */
    public static function update(array $settings): void
    {
        foreach ($settings as $key => $value) {
            if($value == 'on'){
                $value = 1;
            }
            if(is_array($value)){
                $value = json_encode($value);
            }
            Setting::set($key, $value);
        }
    }
}