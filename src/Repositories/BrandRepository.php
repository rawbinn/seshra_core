<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Seshra\Core\Exceptions\GeneralException;
use Seshra\Core\Traits\SlugTrait;

/**
 * Class BrandRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class BrandRepository extends Repository
{
    use SlugTrait;
    
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Brand';
    }

    public function create(array $data)
    {
        try {
            $data['slug'] = $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['name'])));
            
            $model = app()->make($this->model());
            foreach (locales() as $locale) {
                foreach ($model->translatedAttributes as $attribute) {
                    if (isset($data[$attribute])) {
                        $data[$locale->code][$attribute] = $data[$attribute];
                        $data[$locale->code]['locale_id'] = $locale->id;
                    }
                }
            }
            
            $brand = $this->model->create($data);
            
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $brand;
    }

    public function update(array $data, $id)
    {
        try {
            $brand = $this->find($id);
            if($data['slug'] != $brand->slug) {
                $data['slug'] = $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['slug'])));
            }
            $brand->update($data);
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $brand;
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
}