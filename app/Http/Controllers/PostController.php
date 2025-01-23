<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator as Phengly;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->get();
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'image' => ($post->image != null) ? asset('images/' . $post->image) : 'No Image Available',
            ];
        }
        return response()->json([
            'status' => true,
            'message' => 'Select Post Successfully',
            'posts' => $data
        ], 200);
    }

    public function getOnePost($id){
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
               'message' => 'Post not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'image' => ($post->image!= null)? asset('images/'. $post->image) : 'No Image Available',
            ]
        ], 200);
    }

    public function store(Request $request)
    {
        //check validation
        $validator = Phengly::make($request->all(), [
            'title' => 'required|min:5',
        ]);
        // if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'errors' => $validator->errors()
            ], 500);
        }
        //create a new post to database
        $post = new Post();
        $post->title = $request->title;
        $post->description = $request->description;
        if ($request->file('image') != null) {
            $file = $request->file('image');
            $image = rand(000, 9999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $image);
            $post->image = $image;
        }
        $post->save();
        return response()->json([
            'status' => true,
            'message' => 'Post created successfully',
            'post' => $post
        ], 201);
    }
    public function edit($id)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }
        // convert the post
        $data = [
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            // convert the image path
            'image' => ($post->image != null) ? asset('images/' . $post->image) : 'No Image Available',
        ];
        return response()->json([
            'status' => true,
            'message' => 'Select Post Successfully',
            'post' => $data
        ], 200);
    }

    public function update(Request $request, $id)
    {
        //check validation
        $validator = Phengly::make($request->all(), [
            'title' => 'required|min:5',
        ]);
        // if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'errors' => $validator->errors()
            ], 500);
        }
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }
        $post->title = $request->title;
        $post->description = $request->description;
        // if request has old file image
        if ($request->file('image')) {
            $image_path = public_path('images/' . $post->image);
            // delete old image
            if (File::exists($image_path)) {
                File::delete($image_path);
            }
        }
        // if request not have file
        if ($request->file('image') != null) {
            // add new image to database
            $file = $request->file('image');
            $image = rand(000, 9999999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $image);
            $post->image = $image;
        }
        $post->update();
        return response()->json([
            'status' => true,
            'message' => 'Post updated successfully',
            'post' => $post
        ], 201);
    }

    public function delete($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }
        if ($post->image != null) {
            $image = public_path('images/' . $post->image);
            if (File::exists($image)) {
                File::delete($image);
            }
        }
        $post->delete();
        return response()->json([
            'status' => true,
            'message' => 'Post deleted successfully'
        ], 200);
    }
}
