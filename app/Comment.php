<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass asigneble #endregion
     * 
     * @var array
     */
    protected $fillable = ['text', 'author'];

    /**
     * 
     */
    public function post() {
        return $this->belongsTo('App\Post');
    }
}
