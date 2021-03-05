<?php

namespace Seshra\Core\Models;

use Seshra\Media\Models\MediaProxy;
use Seshra\Core\Eloquent\TranslatableModel;
use Seshra\Core\Contracts\Product as ProductContract;
use Seshra\Core\Models\Traits\ActiveScope;
use Seshra\Core\Models\Traits\ProductAttribute;

/**
 * Class Product
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Product extends TranslatableModel implements ProductContract
{

    use ProductAttribute, ActiveScope;

    public $translatedAttributes = [
        'name',
        'highlights',
        'long_description'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'sku',
        'slug',
        'video_url',
        'brand_id',
        'retail_price',
        'tax',
        'tax_type',
        'discount',
        'discount_type',
        'stock',
        'warranty_id',
        'warranty_period_id',
        'meta_title',
        'meta_description',
        'meta_image',
        'status'
    ];

    protected $with = ['translations'];
    
    /**
     * The categories that belong to the product.
     */
    public function categories()
    {
        return $this->belongsToMany(CategoryProxy::modelClass(), 'product_categories');
    }

    public function images()
    {
        return $this->belongsToMany(MediaProxy::modelClass(), 'product_images')->wherePivot('type', 1);
    }

    public function thumbnails()
    {
        return $this->belongsToMany(MediaProxy::modelClass(), 'product_images')->wherePivot('type', 0);
    }

    public function dimension()
    {
        return $this->hasOne(ProductDimensionProxy::modelClass());
    }

    public function tag()
    {
        return $this->hasOne(ProductTagProxy::modelClass());
    }

    public function variations()
    {
        return $this->hasMany(ProductVariationProxy::modelClass());
    }

    public function attributeOptions()
    {
        return $this->hasMany(ProductAttributeOptionProxy::modelClass());
    }

    public function getCategoryAttribute()
    {
        return $this->categories()->orderBy('level', 'DESC')->first();
    }

    public function getImageIds($type = 'normal')
    {
        if($type == 'normal') {
            $ids = $this->images->pluck('id')->toArray();
            return implode(',', $ids);
        }
        else {
            $ids = $this->thumbnails()->pluck('uploads.id')->toArray();
            return implode(',', $ids);
        }
        return '';
    }

}
