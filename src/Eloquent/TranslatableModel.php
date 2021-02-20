<?php

namespace Seshra\Core\Eloquent;

use Seshra\Core\Models\Locale;
use Seshra\Core\Helpers\Locales;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class TranslatableModel extends Model
{
    use Translatable;
    
    protected function getLocalesHelper(): Locales
    {
        return app(Locales::class);
    }

    /**
     * @param  string  $key
     * @return bool
     */
    protected function isKeyALocale($key)
    {
        $chunks = explode('-', $key);

        if (count($chunks) > 1) {
            if (Locale::where('code', '=', end($chunks))->first()) {
                return true;
            }
        } elseif (Locale::where('code', '=', $key)->first()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function locale()
    {
        if ($this->defaultLocale) {
            return $this->defaultLocale;
        }

        return config('translatable.locale') ?: app()->make('translator')->getLocale();
    }

    public function setlocale($data)
    {
        foreach (locales() as $locale) {
            foreach ($this->translatedAttributes as $attribute) {
                if (isset($data[$attribute])) {
                    $data[$locale->code][$attribute] = $data[$attribute];
                }
            }
        }
        return $data;
    }
}