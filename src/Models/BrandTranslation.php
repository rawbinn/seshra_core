<?php

namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\BrandTranslation as BrandTranslationContract;

/**
 * Class BrandTranslation
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class BrandTranslation extends Model implements BrandTranslationContract
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'locale_id'
    ];
}