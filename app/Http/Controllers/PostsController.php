<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostFormRequest;
use App\Models\Post;
use Auth;

use App\Http\Requests;
use Response;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Post::findByRequest()
            ->with('tagged')
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PostFormRequest $request
     * @return Response
     */
    public function store(PostFormRequest $request)
    {
        $post = new Post($request->all());

        Auth::user()
            ->posts()
            ->save($post);

        if ($tags = $request->input('tags')) {
            $post->retag($request->input('tags'));
        }

        return $post->load('tagged');
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Response
     */
    public function show($post)
    {
        return $post;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PostFormRequest $request
     * @param Post $post
     * @return Response
     */
    public function update(PostFormRequest $request, $post)
    {
        $post->fill($request->all());
        $post->save();

        if ($tags = $request->input('tags')) {
            $post->retag($request->input('tags'));
        }

        return $post->load('tagged');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param PostFormRequest $request
     * @param  Post $post
     * @return Response
     */
    public function destroy(PostFormRequest $request, $post)
    {
        $post->delete();

        return 'Post deleted';
    }
}
