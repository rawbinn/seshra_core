<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Seshra\Core\Exceptions\GeneralException;

/**
 * Class CategoryRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class CategoryRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Category';
    }

    public function create(array $data)
    {
        try {
            $data['digital'] = $data['type'];
            $data['parent_id'] = $data['parent_category'];
            unset($data['type']);
            unset($data['parent_category']);
            if($data['parent_id'] != '0') {
                $parent = $this->model->find($data['parent_id']);
                $data['level'] = $parent->level + 1;
            }
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
            $category = $this->model->create($data);
            
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $category;
    }

    public function update(array $data, $id)
    {
        try {
            $category = $this->find($id);
            $data['digital'] = $data['type'];
            $data['parent_id'] = $data['parent_category'];
            unset($data['type']);
            unset($data['parent_category']);
            if($data['parent_id'] != '0') {
                $parent = $this->model->find($data['parent_id']);
                $data['level'] = $parent->level + 1;
            }
            if($data['slug'] != $category->slug) {
                $data['slug'] = $this->slug(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $data['slug'])));
            }
            $category->update($data);
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $category;
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

    private function slug($slug)
    {
        if($this->model->where('slug', $slug)->count() > 0) {
            $update_slug = $slug.'-'.mt_rand(1,9);
            $this->slug($update_slug);
        }
        return strtolower($slug);
    }

    public function getAllWithChildren()
    {
        return $this->model->with('children')->where('parent_id', 0)->get();
    }
}