<?php
namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\Country as CountryContract;
use Seshra\Core\Models\Traits\ActiveScope;

class Country extends Model implements CountryContract
{
    use ActiveScope;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'status'
    ];
}
