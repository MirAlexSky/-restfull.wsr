<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
     * get posts with tags
     */
    public function withTags() {
        return [
            'title' => $this->title,
            'datetime' => $this->created_at,
            'anons' => $this->anons,
            'text' => $this->text,
            'tags' => $this->tags->pluck('name')->toArray(),
            'image' => asset(Storage::url($this->image)),
        ];

       // Post::all()
    }

    /**
     * add new Tag
     * 
     * @param String $tags
     */
    public function addTags(String $tags) 
    {
        if ($tags != "") {
            $tags = explode(',', $tags);
            $tags = array_map('trim', $tags);
   
            foreach ($tags as $tag) {
                $tagToPost = Tag::firstOrNew(['name' => $tag]);
                $tagToPost->save();

                $tags_id[] = $tagToPost->id;
            }

            $this->tags()->attach($tags_id);
        }
    }

    /**
     * get posts with tags
     */
    public function full() {
        return [
            'title' => $this->title,
            'datetime' => $this->created_at,//->format(), //carbon
            'anons' => $this->anons,
            'text' => $this->text,
            'tags' => $this->tags()->pluck('name')->toArray(),
            'image' => asset(Storage::url($this->image)),
            'comments' => $this->comments()->get()->toArray(),
        ];
    }


    /**
     * 
     */
    public function tags() {
        return $this->belongsToMany('App\Tag')->withTimestamps();
    }
}
