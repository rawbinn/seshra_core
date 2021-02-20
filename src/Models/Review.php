<?php
namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\Review as ReviewContract;

class Review extends Model implements ReviewContract
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'rating',
        'comment',
        'status',
        'viewed'
    ];
}
