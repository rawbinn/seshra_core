<?php

namespace Seshra\Core\Models;

use Seshra\Core\Contracts\Attribute as AttributeContract;
use Seshra\Core\Eloquent\TranslatableModel;

/**
 * Class Attribute
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Attribute extends TranslatableModel implements AttributeContract
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
        'name',
        'type',
        'is_filterable'
    ];

    protected $with = ['translations'];

    public function options()
    {
        return $this->hasMany(AttributeOptionProxy::modelClass(), 'attribute_id');
    }
}
