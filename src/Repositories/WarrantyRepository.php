<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Seshra\Core\Exceptions\GeneralException;

/**
 * Class WarrantyRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class WarrantyRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Warranty';
    }


    public function delete($id)
    {
        $model = $this->findOrFail($id);
        try{
            parent::delete($id);
            // $model->deleteTranslations();
            return true;
        }
        catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }
    }
}