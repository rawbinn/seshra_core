<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;
use Seshra\Core\Exceptions\GeneralException;

class ReviewRepository extends Repository
{

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Seshra\Core\Contracts\Review';
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