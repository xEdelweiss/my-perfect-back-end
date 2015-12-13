<?php

namespace App\Http\Requests;

class PostFormRequest extends Request
{
    /**
     * Name of entity which is controlled by this request.
     *
     * @var string
     */
    protected $modelKey = 'posts';

    /**
     * Rules for this request.
     *
     * @var array
     */
    public $rules = [
        self::CREATE => [
            'title' => 'required|min:10|max:255|unique:posts',
            'intro' => 'required|min:10',
            'text' => 'required|min:50',
        ],
        self::UPDATE => [
            'title' => 'required|min:10|max:255|unique:posts,title,:id',
            'intro' => 'required|min:10',
            'text' => 'required|min:50',
        ],
        self::DELETE => [],
    ];
}
