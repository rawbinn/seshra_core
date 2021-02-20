<?php

namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\ProductAttributeOption as ProductAttributeOptionContract;

/**
 * Class ProductAttributeOption
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class ProductAttributeOption extends Model implements ProductAttributeOptionContract
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'attribute_id',
        'attribute_option_id',
        'text'
    ];
}
