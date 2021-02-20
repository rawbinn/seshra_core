<?php

namespace Seshra\Core;

use Illuminate\Support\Str;
use Seshra\Core\Repositories\LocaleRepository;
use Seshra\Core\Repositories\TranslationRepository;

/**
 * Class Core
 * @package Seshra\Core
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Core
{

    /**
     * LocaleRepository class
     *
     * @var \Seshra\Core\Repositories\LocaleRepository
     */
    protected $localeRepository;

    /**
     * TranslationRepository class
     *
     * @var \Seshra\Core\Repositories\TranslationRepository
     */
    protected $translationRepository;

    /**
     * Create a new instance.
     *
     * @param \Seshra\Core\Repositories\LocaleRepository        $localeRepository
     * @param \Seshra\Core\Repositories\TranslationRepository        $translationRepository
     *
     * @return void
     */
    public function __construct(
        LocaleRepository $localeRepository,
        TranslationRepository $translationRepository
    )
    {
        $this->localeRepository = $localeRepository;
        $this->translationRepository = $translationRepository;
    }

    /**
     * Returns all locales
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllLocales()
    {
        static $locales;

        if ($locales) {
            return $locales;
        }

        return $locales = $this->localeRepository->all();
    }

    /**
     * Returns current locale
     *
     * @return \Seshra\Core\Contracts\Locale
     */
    public function getCurrentLocale()
    {
        static $locale;

        if ($locale) {
            return $locale;
        }

        $locale = $this->localeRepository->findOneByField('code', app()->getLocale());

        if (! $locale) {
            $locale = $this->localeRepository->findOneByField('code', config('app.fallback_locale'));
        }

        return $locale;
    }

    public function translate($key, $locale = null)
    {
        if(!$locale){
            $locale = app()->getLocale();
        }
        $locale = $locale ?? config('app.fallback_locale');
        $slug = Str::snake($key);
        $translation = $this->translationRepository->findWhere(['lang' => $locale, 'lang_key' => $slug])->first();
        if(!$translation) {
            $translation = $this->translationRepository->create([
                'lang' => $locale,
                'lang_key' => $slug,
                'lang_value' => $key
            ]);
        }
        return $translation->lang_value;
    }

     /**
     * Method to sort through the acl items and put them in order
     *
     * @param array $items
     *
     * @return array
     */
    public function sortItems($items)
    {
        foreach ($items as &$item) {
            if (count($item['children'])) {
                $item['children'] = $this->sortItems($item['children']);
            }
        }

        usort($items, function ($a, $b) {
            if ($a['sort'] == $b['sort']) {
                return 0;
            }

            return ($a['sort'] < $b['sort']) ? -1 : 1;
        });

        return $this->convertToAssociativeArray($items);
    }

    
    /**
     * @param array $items
     *
     * @return array
     */
    public function convertToAssociativeArray($items)
    {
        foreach ($items as $key1 => $level1) {
            unset($items[$key1]);
            $items[$level1['key']] = $level1;

            if (count($level1['children'])) {
                foreach ($level1['children'] as $key2 => $level2) {
                    $temp2 = explode('.', $level2['key']);
                    $finalKey2 = end($temp2);
                    unset($items[$level1['key']]['children'][$key2]);
                    $items[$level1['key']]['children'][$finalKey2] = $level2;

                    if (count($level2['children'])) {
                        foreach ($level2['children'] as $key3 => $level3) {
                            $temp3 = explode('.', $level3['key']);
                            $finalKey3 = end($temp3);
                            unset($items[$level1['key']]['children'][$finalKey2]['children'][$key3]);
                            $items[$level1['key']]['children'][$finalKey2]['children'][$finalKey3] = $level3;
                        }
                    }

                }
            }
        }

        return $items;
    }

    /**
     * @param array            $items
     * @param string           $key
     * @param string|int|float $value
     *
     * @return array
     */
    public function array_set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);
        $count = count($keys);

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $finalKey = array_shift($keys);

        if (isset($array[$finalKey])) {
            $array[$finalKey] = $this->arrayMerge($array[$finalKey], $value);
        } else {
            $array[$finalKey] = $value;
        }

        return $array;
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    protected function arrayMerge(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = $this->arrayMerge($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * @param array $array1
     *
     * @return array
     */
    public function convertEmptyStringsToNull($array)
    {
        foreach ($array as $key => $value) {
            if ($value == "" || $value == "null") {
                $array[$key] = null;
            }
        }

        return $array;
    }

    /**
     * Create singletom object through single facade
     *
     * @param string $className
     *
     * @return object
     */
    public function getSingletonInstance($className)
    {
        static $instance = [];

        if (array_key_exists($className, $instance)) {
            return $instance[$className];
        }

        return $instance[$className] = app($className);
    }

}