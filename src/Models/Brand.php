<?php

namespace Seshra\Core\Models;

use Seshra\Core\Contracts\Brand as BrandContract;
use Seshra\Core\Eloquent\TranslatableModel;

/**
 * Class Brand
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Brand extends TranslatableModel implements BrandContract
{
    
    public $translatedAttributes = [
        'name'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'logo',
        'top',
        'slug',
        'meta_title',
        'meta_description'
    ];

    protected $with = ['translations'];
}