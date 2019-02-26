<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Comment;
use Validator;
use App\User;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $posts = Post::all();

        foreach ($posts as $post) {
            $response[] = [
                'title' => $post->title,
                'datetime' => $post->created_at,
                'anons' => $post->anons,
                'text' => $post->text,
                'tags' => $post->tags()->pluck('name')->toArray(),
                'image' => $post->image,
            ];
        }

        return response()->json($response)->setStatusCode(200, 'List posts');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:posts,title',
            'anons' => 'required',
            'text'  => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()->getMessages()
            ])->setStatusCode(400, 'Creating error');
        }

        $newPost = new Post;
        $newPost->title = $request->title;
        $newPost->anons = $request->anons;
        $newPost->text = $request->text;
        $newPost->image = "imagePath";
       
        // Do file upload
        // $newPost->image = $request->image;
        $newPost->save();

        if ($request->tags != "") {
            $tags = explode(',', $request->tags);
            $tags = array_map('trim', $tags);
   
            foreach ($tags as $tag) {
                $tagToPost = Tag::where('name', $tag)->first();

                if ($tagToPost == null) 
                {
                    $tagToPost = new Tag;
                    $tagToPost->name = $tag;
                    $tagToPost->save(); 
                }

                $tags_id[] = $tagToPost->id;
            }

            $newPost->tags()->attach($tags_id);
        }

        return response()->json([
            'status' => 'true',
            'post_id' => $newPost->id,
        ])->setStatusCode(201, 'Successful creation');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        $response = [
            'title' => $post->title,
            'datetime' => $post->created_at,
            'anons' => $post->anons,
            'text' => $post->text,
            'tegs' => $post->tags()->pluck('name')->toArray(),
            'image' => $post->image,
            'comments' => $post->comments()->get()->toArray()
        ];

        return response()->json($response)->setStatusCode(200, 'List posts');

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|unique:posts,title',
            'anons' => 'required',
            'text'  => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()->getMessages()
            ])->setStatusCode(400, 'Editing error');
        }


        $post = Post::find($id);

        if ($post === null) {
            return response()->json([
                'message' => 'Post not found'
            ])->setStatusCode(404, 'Post not found');
        }

        $post->title = $request->title;
        $post->anons = $request->anons;
        $post->text = $request->text;
        $post->image = "imagePath";

        $post->save();

        if ($request->tags != "") {
            $tags = explode(',', $request->tags);
            $tags = array_map('trim', $tags);

            foreach ($tags as $tag) {
                $tagToPost = Tag::where('name', $tag)->first();

                if ($tagToPost === null) 
                {
                    $tagToPost = new Tag;
                    $tagToPost->name = $tag;
                    $tagToPost->save(); 
                }

                $tags_id[] = $tagToPost->id;
            }

            $post->tags()->sync($tags_id);
        }

        return response()->json([
            'status' => 'true',
            'post' => [
                'title' => $post->title,
                'datetime' => $post->created_at,
                'anons' => $post->anons,
                'text' => $post->text,
                'tegs' => $post->tegs()->pluck('name')->toArray(),
                'image' => $post->image,

            ],
        ])->setStatusCode(201, 'Successful creation');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);
        if ($post == null) {
            return response()->json([
                'message' => 'post not found'
            ])->setStatusCode(404, 'post not found');
        }

        $post->delete();
        return response()->json([
            'status' => 'true'
        ])->setStatusCode(201, 'Successfull delete');
    }

    public function createComment($post_id) {
        $post = Post::find($post_id);

        $post->comments()->insert();
    }

}
