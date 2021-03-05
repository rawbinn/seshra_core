<?php
namespace Seshra\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Seshra\Core\Contracts\District as DistrictContract;
use Seshra\Core\Models\Traits\ActiveScope;

class District extends Model implements DistrictContract
{
    use ActiveScope;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'state_id',
        'status'
    ];

    public function state()
    {
        return $this->belongsTo(StateProxy::modelClass());
    }
}
