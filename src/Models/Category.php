<?php

namespace Seshra\Core\Models;

use Seshra\Core\Eloquent\TranslatableModel;
use Seshra\Core\Contracts\Category as CategoryContract;

/**
 * Class Category
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class Category extends TranslatableModel implements CategoryContract
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
        'parent_id',
        'name',
        'level',
        'banner',
        'icon',
        'featured',
        'top',
        'digital',
        'slug',
        'commission',
        'meta_title',
        'meta_description',
        'created_at',
        'update_at'
    ];
    
    protected $with = ['translations'];

    public function parent()
    {
        return $this->belongsTo(CategoryProxy::modelClass(), 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CategoryProxy::modelClass(), 'parent_id');
    }

    public function parents()
    {
        $parents = collect([]);
        if($parent = $this->parent) {
            // level 1
            $parents->push($parent);
            if($grand_parent = $parent->parent) {
                // level 2
                $parents->push($grand_parent);
                if($great_parent = $grand_parent->parent) {
                    // level 3
                    $parents->push($great_parent);
                    if($great_grand_parent = $great_parent->parent) {
                        // level 4
                        $parents->push($great_grand_parent);
                    }
                }
            }
        }
        return $parents;
    }

}
