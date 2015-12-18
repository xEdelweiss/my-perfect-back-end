<?php

use App\Models\Post;
use App\Models\User;

class PostsTest extends TestCase
{
    public function testShow()
    {
        /** @var \App\Models\Post $post */
        $post = factory(\App\Models\Post::class)->create();

        // show
        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(200)
            ->assertDataKeysEqual([
                'title' => $post->title,
                'intro' => $post->intro,
                'text' => $post->text,
                'author_id' => $post->author_id,
            ]);
    }

    public function testIndex()
    {
        factory(\App\Models\Post::class, 5)->create();
        $postsCount = \App\Models\Post::all()->count();

        // index
        $this
            ->logout()
            ->request('GET', 'api/posts')
            ->assertStatus(200)
            ->assertKeyExists('result.data.0.id')
            ->assertKeyChildrenCountEquals('result.data', $postsCount); // @TODO pagination?
    }

    public function testIndexByIds()
    {
        /** @var \App\Models\Post[] $posts */
        $posts = factory(\App\Models\Post::class, 2)->create();

        $this
            ->logout()
            ->request('GET', 'api/posts', [
                'query' => [
                    'id' => [
                        $posts[0]->id,
                        $posts[1]->id
                    ]
                ]
            ])
            ->assertStatus(200)
            ->assertKeysExist([
                'result.data.0.id',
                'result.data.1.id',
            ])
            ->assertKeyChildrenCountEquals('result.data', 2);
    }

    public function testIndexByTag()
    {
        $uniqueTag = uniqid();
        factory(\App\Models\Post::class)->create()->tag($uniqueTag);

        $this
            ->logout()
            ->request('GET', 'api/posts', [
                'query' => [
                    'tag' => $uniqueTag,
                ]
            ])
            ->assertStatus(200)
            ->assertKeysExist([
                'result.data.0.id',
            ])
            ->assertKeyChildrenCountEquals('result.data', 1);
    }

    public function testCreateByGuest()
    {
        $this
            ->logout()
            ->request('POST', 'api/posts', [
                'json' => factory(Post::class)->make(),
            ])
            ->assertStatus(403);
    }

    public function createWithValidationErrors()
    {
        /** @var User $user */
        $user = factory(\App\Models\User::class)->create();

        $this
            ->login($user->id)
            ->request('POST', 'api/posts', [
                'json' => [],
            ])
            ->assertStatus(400)
            ->assertKeyNotExists('result.data.id')
            ->assertKeysExist([
                'result.errors.title',
                'result.errors.intro',
                'result.errors.text',
            ]);
    }

    public function testCreate()
    {
        $tags = $this->getTags();

        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->make();

        $this
            ->login($post->author_id)
            ->request('POST', 'api/posts', [
                'json' => [
                    'title' => $post->title,
                    'intro' => $post->intro,
                    'text' => $post->text,
                    'tags' => $tags,
                ],
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.id')
            ->assertDataKeysEqual([
                'title' => $post->title,
                'intro' => $post->intro,
                'text' => $post->text,
                'author_id' => $post->author_id,
            ])
            ->assertDataKeysEqual([
                'tagged.0.tag_slug' => $tags[0],
                'tagged.1.tag_slug' => $tags[1],
                'tagged.2.tag_slug' => $tags[2],
            ])
            ->assertKeyChildrenCountEquals('result.data.tagged', 3)
            ->getId();
    }

    public function testUpdateByGuest()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $this
            ->logout()
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [
                    'title' => $post->title,
                    'intro' => $post->intro,
                    'text' => $post->text,
                ],
            ])
            ->assertStatus(403);
    }

    public function testUpdateByNotOwner()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        /** @var User $user */
        $user = factory(\App\Models\User::class)->create();

        $this
            ->login($user->id)
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [
                    'title' => $post->title,
                    'intro' => $post->intro,
                    'text' => $post->text,
                ],
            ])
            ->assertStatus(403);
    }

    public function testDeleteByNotOwner()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        /** @var User $user */
        $user = factory(\App\Models\User::class)->create();

        $this
            ->login($user->id)
            ->request('DELETE', "api/posts/{$post->id}")
            ->assertStatus(403);
    }

    public function testUpdateWithValidationErrors()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $this
            ->login($post->author_id)
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [],
            ])
            ->assertStatus(400)
            ->assertKeyNotExists('result.data.id')
            ->assertKeysExist([
                'result.errors.title',
                'result.errors.intro',
                'result.errors.text',
            ]);
    }

    public function testUpdateByOwner()
    {
        $tags = $this->getTags();

        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();
        $post->tag($tags);

        $this
            ->login($post->author_id)
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [
                    'title' => $post->title,
                    'intro' => $post->intro,
                    'text' => $post->text,
                    'tags' => $tags,
                ],
            ])
            ->assertStatus(200)
            ->assertDataKeysEqual([
                'id' => $post->id,
                'title' => $post->title,
                'intro' => $post->intro,
                'text' => $post->text,
                'author_id' => $post->author_id,
            ])
            ->assertDataKeysEqual([
                'tagged.0.tag_slug' => $tags[0],
                'tagged.1.tag_slug' => $tags[1],
                'tagged.2.tag_slug' => $tags[2],
            ])
            ->assertKeyChildrenCountEquals('result.data.tagged', 3);
    }

    public function testOwnerCannotBeChanged()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        /** @var User $user */
        $user = factory(User::class)->create();

        $this
            ->login($post->author_id)
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [
                    'title' => $post->title,
                    'intro' => $post->intro,
                    'text' => $post->text,
                    'author_id' => $user->id, // testing this
                ],
            ])
            ->assertStatus(200)
            ->assertDataKeysEqual([
                'author_id' => $post->author_id,
            ]);
    }

    public function testDeleteByOwner()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $this
            ->login($post->author_id)
            ->request('DELETE', "api/posts/{$post->id}")
            ->assertStatus(200);

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(404)
            ->assertKeyNotExists('result.data.id');
    }

    public function testVisibilityIndex()
    {
        /** @var User $user */
        $user = factory(\App\Models\User::class)->create();

        factory(\App\Models\Post::class, 2)->create([
            'is_draft' => true,
            'is_private' => false,
            'author_id' => $user->id,
        ]);
        factory(\App\Models\Post::class, 2)->create([
            'is_draft' => false,
            'is_private' => true,
            'author_id' => $user->id,
        ]);
        factory(\App\Models\Post::class, 2)->create([
            'is_draft' => true,
            'is_private' => true,
            'author_id' => $user->id,
        ]);
        factory(\App\Models\Post::class, 2)->create([
            'is_draft' => false,
            'is_private' => false,
            'author_id' => $user->id,
        ]);

        // index as guest
        $this
            ->logout()
            ->request('GET', 'api/posts', [
                'query' => [
                    'author_id' => $user->id,
                ],
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.0.id')
            ->assertKeyChildrenCountEquals('result.data', 2);

        // authorized as owner
        $this
            ->login($user->id)
            ->request('GET', 'api/posts', [
                'query' => [
                    'author_id' => $user->id,
                ],
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.0.id')
            ->assertKeyChildrenCountEquals('result.data', 8);
    }

    public function testVisibleShowByGuest()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create([
            'is_draft' => false,
            'is_private' => false,
        ]);

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(200)
            ->assertKeyExists('result.data.id');
    }

    public function testDraftShowByGuest()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create([
            'is_draft' => true,
            'is_private' => false,
        ]);

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(404)
            ->assertKeyNotExists('result.data.id');
    }

    public function testPrivateShowByGuest()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create([
            'is_draft' => false,
            'is_private' => true,
        ]);

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(404)
            ->assertKeyNotExists('result.data.id');
    }

    public function testDraftShowByOwner()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create([
            'is_draft' => true,
            'is_private' => false,
        ]);

        $this
            ->login($post->author_id)
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(200)
            ->assertKeyExists('result.data.id');
    }

    public function testPrivateShowByOwner()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create([
            'is_draft' => false,
            'is_private' => true,
        ]);

        $this
            ->login($post->author_id)
            ->request('GET', "api/posts/{$post->id}")
            ->assertStatus(200)
            ->assertKeyExists('result.data.id');
    }
}
