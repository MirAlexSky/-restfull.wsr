<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    /**
     * datetime format
     */
    protected $casts = ['created_at' => 'datetime:H:00 Y-m-d'];

    /**
     * The attributes that are mass asigneble #endregion
     * 
     * @var array
     */
    protected $fillable = ['text', 'author'];

    /**
     * Attributes that are visible
     */
    protected $visible = ['id', 'created_at', 'author', 'text'];

    /**
     * 
     */
    public function post() {
        return $this->belongsTo('App\Post');
    }
}
