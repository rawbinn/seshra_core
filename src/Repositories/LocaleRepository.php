<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Seshra\Core\Exceptions\GeneralException;

/**
 * Class LocaleRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class LocaleRepository extends Repository
{
    
    protected $cacheOnly = ['all'];

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Locale';
    }

    public function create(array $data)
    {
        parent::create($data);
    }

    public function delete($id)
    {
        $model = $this->findOrFail($id);
        try{
            parent::delete($id);
            return true;
        }
        catch(\Exception $e) {
            throw new GeneralException($e->getMessage());
        }
    }
}