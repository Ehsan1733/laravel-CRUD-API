<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['user_id','name','link','amount','brand_id','code','type','status'];

    /**
     * Get the brand that owns the brand_id.
     */
    public function brand()
    {
        return $this->belongsTo('App\brand')->select('id', 'name');
    }
}
