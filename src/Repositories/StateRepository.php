<?php

namespace Seshra\Core\Repositories;

use Seshra\Core\Eloquent\Repository;

/**
 * Class StateRepository
 * @package Seshra\Core\Repositories
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class StateRepository extends Repository
{
    protected $cacheOnly = ['all'];

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return 'Seshra\Core\Contracts\State';
    }

}