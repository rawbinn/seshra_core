<?php
namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\State as StateContract;
use Seshra\Core\Models\Traits\ActiveScope;

class State extends Model implements StateContract
{
    use ActiveScope;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'country_id',
        'status'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
