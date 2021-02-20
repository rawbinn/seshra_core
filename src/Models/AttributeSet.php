<?php

namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\AttributeSet as AttributeSetContract;

/**
 * Class AttributeSet
 * @package Seshra\Core\Models
 * @version 1.0.0
 * @author Rawbinn Shrestha <contact@rawbinn.com>
 * @organization RAWBINN.COM.
 */
class AttributeSet extends Model implements AttributeSetContract
{
    public function attribute()
    {
        return $this->belongsTo(AttributeProxy::modelClass(), 'attribute_id');
    }
}