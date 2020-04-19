<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name','description','category_id','website'];

    /**
     * Get the Categories that owns the category_id.
     */
    public function brand()
    {
        return $this->belongsTo('App\Category');
    }
}
