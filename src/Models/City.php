<?php
namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\City as CityContract;
use Seshra\Core\Models\Traits\ActiveScope;

class City extends Model implements CityContract
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
        'state_id',
        'district_id',
        'status'
    ];

    public function country()
    {
        return $this->belongsTo(CountryProxy::modelClass());
    }

    public function state()
    {
        return $this->belongsTo(StateProxy::modelClass());
    }

    public function district()
    {
        return $this->belongsTo(DistrictProxy::modelClass());
    }
}
