<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Seshra\Core\Exceptions\GeneralException;

/**
 * Class AttributeOptionRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeOptionRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\AttributeOption';
    }

    public function create(array $data)
    {
        try {
            $model = app()->make($this->model());
            foreach (locales() as $locale) {
                foreach ($model->translatedAttributes as $attribute) {
                    if (isset($data[$attribute])) {
                        $data[$locale->code][$attribute] = $data[$attribute];
                    }
                }
            }
            $attribute = $this->model->create($data);
            
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $attribute;
    }

    public function update(array $data, $id)
    {
        try {
            $attribute = $this->find($id);
            $attribute->update($data);
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $attribute;
    }

    public function delete($id)
    {
        $model = $this->findOrFail($id);
        try{
            parent::delete($id);
            $model->deleteTranslations();
            return true;
        }
        catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }
    }

    public function isNameExist($name, $attribute_id, $attribute_option_id = null, $locale = null)
    {
        if($locale == null) {
            $locale = config('app.fallback_locale');
        }
        $option = $this->model->leftjoin('attribute_option_translations', 'attribute_options.id','=','attribute_option_translations.attribute_option_id')->where('attribute_options.attribute_id', $attribute_id)->where('attribute_option_translations.locale', $locale)->where('attribute_option_translations.name','=', $name);
        if($attribute_option_id!=null){
            $option = $option->where('attribute_options.id','<>', $attribute_option_id);
        }
        return $option->count() > 0;
    }
}