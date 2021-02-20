<?php

namespace Seshra\Core\Repositories;

use DB;
use Seshra\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Seshra\Core\Exceptions\GeneralException;

/**
 * Class AttributeRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Attribute';
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

    public function addAttributeToSet($attribute_set_id, $attributes)
    {
        foreach($attributes as $attribute){
            if(DB::table('attribute_sets')->where('category_id', $attribute_set_id)->where('attribute_id', $attribute)->count() == 0){
                DB::table('attribute_sets')->insert([
                    'category_id' => $attribute_set_id,
                    'attribute_id' => $attribute
                ]);
            }
        }
    }

    public function removeAttributeFromSet($attribute_set_id, $attributes)
    {
        foreach($attributes as $attribute){
            DB::table('attribute_sets')->where('category_id', $attribute_set_id)->where('attribute_id', $attribute)->delete();
        }
    }

    public function getAllAttributesOfSet($attribute_set_id)
    {
        $attribute_ids = DB::table('attribute_sets')->select('attribute_id')->where('category_id', $attribute_set_id)->pluck('attribute_id')->toArray();
        if(count($attribute_ids) > 0) {
            return $this->model->whereIn('id', $attribute_ids)->get();
        }
        return [];
    }
}