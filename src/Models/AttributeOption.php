<?php

namespace Seshra\Core\Models;

use Seshra\Core\Contracts\AttributeOption as AttributeOptionContract;
use Seshra\Core\Eloquent\TranslatableModel;

/**
 * Class AttributeOption
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeOption extends TranslatableModel implements AttributeOptionContract
{
    public $timestamps = false;

    public $translatedAttributes = [
        'name'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attribute_id',
        'name',
        'sort'
    ];

    protected $with = ['translations'];

    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass(), 'attribute_id');
    }

}
