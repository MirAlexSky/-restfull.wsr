<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $casts = ['created_at' => 'datetime:H:00 Y-m-d'];
    protected $visible = ['title', 'created_at', 'anons', 'text', 'image', 'tags', 'comments'];

    /**
     * The attributes that are mass assignable
     * 
     * @var array
     */
    protected $fillable = ['title', 'anons', 'text'];

    /**
     * 
     */
    public function comments() {
        return $this->hasMany('App\Comment');
    }

    /**
     * 
     */
    public function tags() {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
}
