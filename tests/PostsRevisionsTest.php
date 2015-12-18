<?php

use App\Models\Post;

class PostsRevisionsTest extends TestCase
{
    public function testInitialRevision()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(200)
            ->assertDataKeysEqual([
                '0.base_id' => $post->id,
                '0.title' => $post->title,
                '0.intro' => $post->intro,
                '0.text' => $post->text,
                '0.author_id' => $post->author_id,
            ])
            ->assertKeyChildrenCountEquals('result.data', 1);
    }

    public function testNoRevisionWithoutChangesWithInternal()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();
        $post->save();

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(200)
            ->assertKeyChildrenCountEquals('result.data', 1);
    }

    public function testRevisionOnUpdateWithInternal()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $previousTitle = $post->title;

        $post->title .= ' (updated!)';
        $post->save();

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(200)
            ->assertDataKeysEqual([
                '0.base_id' => $post->id,
                '0.title' => $post->title,
                '1.base_id' => $post->id,
                '1.title' => $previousTitle,
            ])
            ->assertKeyChildrenCountEquals('result.data', 2);
    }

    public function testNoRevisionWithoutChangesWithApi()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $this
            ->login($post->author_id)
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [
                    'title' => $post->title,
                    'intro' => $post->intro,
                    'text' => $post->text,
                ],
            ])
            ->assertStatus(200);

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(200)
            ->assertKeyChildrenCountEquals('result.data', 1);
    }

    public function testRevisionOnUpdateWithApi()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $newTitle = $post->title . ' (updated!)';

        $this
            ->login($post->author_id)
            ->request('PUT', "api/posts/{$post->id}", [
                'json' => [
                    'title' => $newTitle,
                    'intro' => $post->intro,
                    'text' => $post->text,
                ],
            ])
            ->assertStatus(200);

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(200)
            ->assertDataKeysEqual([
                '0.base_id' => $post->id,
                '0.title' => $newTitle,
                '0.intro' => $post->intro,
                '0.text' => $post->text,
                '0.author_id' => $post->author_id,
            ])
            ->assertKeyChildrenCountEquals('result.data', 2);
    }

    public function testShow()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create();

        $latestRevisionId = $post->getLastRevision()->id;

        $this
            ->request('GET', "api/posts/{$post->id}/revisions/{$latestRevisionId}")
            ->assertDataKeysEqual([
                'base_id' => $post->id,
                'title' => $post->title,
                'intro' => $post->intro,
                'text' => $post->text,
                'author_id' => $post->author_id,
            ])
            ->assertStatus(200);
    }

    public function testShowAnotherPostsRevision()
    {
        /** @var Post $post */
        $postA = factory(\App\Models\Post::class)->create();
        $postB = factory(\App\Models\Post::class)->create();

        $latestRevisionId = $postB->getLastRevision()->id;

        $this
            ->request('GET', "api/posts/{$postA->id}/revisions/{$latestRevisionId}")
            ->assertStatus(404);
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
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(404)
            ->assertKeyNotExists('result.data.id');
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
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(404)
            ->assertKeyNotExists('result.data.id');
    }

    public function testOnlySecuredInIndexByGuest()
    {
        /** @var Post $post */
        $post = factory(\App\Models\Post::class)->create([
            'is_draft' => true,
            'is_private' => false,
        ]);

        $post->is_draft = false;
        $post->save(); // revision!

        $this
            ->logout()
            ->request('GET', "api/posts/{$post->id}/revisions")
            ->assertStatus(200)
            ->assertKeyChildrenCountEquals('result.data', 1);
    }
}
