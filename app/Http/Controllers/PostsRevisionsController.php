<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests;
use Illuminate\Http\Response;

class PostsRevisionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Post $post
     * @return Response
     */
    public function index($post)
    {
        return $post->revisions;
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @param $revisionId
     * @return Response
     */
    public function show($post, $revisionId)
    {
        return $post->revisions()->findOrFail($revisionId);
    }
}
