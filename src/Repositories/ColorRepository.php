<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Seshra\Core\Exceptions\GeneralException;

/**
 * Class ColorRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ColorRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Color';
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
            $color = $this->model->create($data);
            
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $color;
    }

    public function update(array $data, $id)
    {
        try {
            $color = $this->find($id);
            $color->update($data);
        }catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }

        return $color;
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