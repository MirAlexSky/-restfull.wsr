<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Tag;
use App\Comment;
use Validator;
use App\User;
use Illuminate\Support\Facades\Storage;

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
            $response[] = $post->withTags();
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

        $image = $request->image;
        $imagePath = $image->store('post_images', 'public');

        $newPost = new Post;
        $newPost->title = $request->title;
        $newPost->anons = $request->anons;
        $newPost->text = $request->text;
        $newPost->image = $imagePath;
       
        $newPost->save();

        $newPost->addTags($request->tags);

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

        if (!$post) {
            return response()->json([
                'status' => 'false',
                'message' => 'Post not found',
            ])->setStatusCode(404, 'Post not found');
        }

        return response()->json($post->full())->setStatusCode(200, 'View posts');
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

        // TODO Удаление старого фото
        $image = $request->image;
        $imagePath = $image->store('post_images', 'public');

        $post->title = $request->title;
        $post->anons = $request->anons;
        $post->text = $request->text;
        $post->image = $imagePath;

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
                'tegs' => $post->tags()->pluck('name')->toArray(),
                'image' => asset(Storage::url($post->image)),
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

    /**
     * Store new comment
     * 
     * @param Request $request
     * @return Response $response
     */
    public function comment(Request $request, $post_id) {

        $validator = Validator::make($request->all(), [
            'author' => 'required',
            'comment' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'false',
                'message' => $validator->errors()->getMessages(),
            ])->setStatusCode(400, 'creation error');
        }

        $post = Post::find($post_id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found'
            ])->setStatusCode(404, 'Post not found');
        }

        $post->comments()->save(Comment::create([
            'text' => $request->comment,
            'author' => $request->author
        ]));

        return response()->json([
            'status' => 'true'
        ])->setStatusCode(201, 'Successful creation');

    }

    /**
     * Store new comment
     * 
     * @param Request $request
     * @return Response $response
     */
    public function deleteComment(Request $request, $post_id, $comment_id) 
    {
        $post = Post::find($post_id);

        if (!$post) {
            return response()->json([
                'message' => 'post not found'
            ])->setStatusCode(404, 'Post not found');
        }

        $comment = $post->comments()->find($comment_id);

        if (!$comment) {
            return response()->json([
                'message' => 'comment not found'
            ])->setStatusCode(404, 'comment not found');
        }

        $comment->delete();

        return response()->json([
            'status' => 'true',
        ])->setStatusCode(201, 'Successful delete');
    }

    /**
     * finf post via tag
     * 
     * @param Request $request
     * @return Response $response
     */
    public function tag($tag_name) {
        $tag = Tag::where('name', $tag_name)->first();

        if (!$tag) {
            return response()->json([
                'status' => 'false',
                'message' => 'tag not found',
            ])->setStatusCode(401, 'Not found');
        }

        $posts = $tag->posts()->get();

        foreach ($posts as $post) {
            $postsWithTags[] = $post->withTags();
        }

        return response()->json($postsWithTags)->setStatusCode(200, 'Found posts'); 
    }
}
