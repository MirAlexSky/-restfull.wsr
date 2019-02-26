<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    protected $visible = ['name'];

    /**
     * A attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Posts of the tag
     * 
     */
    public function posts() {
        return $this->belongsToMany('App\Tag');
    }
}
