<?php

namespace Seshra\Core\Models;

use Seshra\Core\Eloquent\TranslatableModel;
use Seshra\Core\Contracts\Color as ColorContract;

/**
 * Class Color
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Color extends TranslatableModel implements ColorContract
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
        'code'
    ];
    
    protected $with = ['translations'];

}
