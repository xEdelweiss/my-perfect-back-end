<?php

class PostsTest extends TestCase
{
    public function testFlow()
    {
        $modelData = $this->modelData();
        $modifiedModelData = $this->modelData();

        /** @var \App\Models\Post[] $posts */
        $posts = factory(\App\Models\Post::class, 5)->create();

        // show
        $this
            ->logout()
            ->request('GET', "api/posts/{$posts[1]->id}")
            ->assertStatus(200)
            ->assertDataKeysEqual($posts[1]->getAttributes());

        // index
        $this
            ->logout()
            ->request('GET', 'api/posts')
            ->assertStatus(200)
            ->assertKeysExist([
                'result.data.0.id',
                'result.data.1.id',
            ]);

        // index by id
        $this
            ->logout()
            ->request('GET', 'api/posts', [
                'query' => [
                    'id' => [$posts[1]->id, $posts[2]->id]
                ]
            ])
            ->assertStatus(200)
            ->assertKeysExist([
                'result.data.0.id',
                'result.data.1.id',
            ])
            ->assertKeyChildrenCountEquals('result.data', 2);

        // index by tag
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

        // create by guest
        $this
            ->logout()
            ->request('POST', 'api/posts', [
                'json' => $this->modelData(),
            ])
            ->assertStatus(403);

        // create with validation errors
        $this
            ->login(1)
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

        // create
        $postId = $this
            ->login(1)
            ->request('POST', 'api/posts', [
                'json' => $modelData,
            ])
            ->assertStatus(200)
            ->assertKeyExists('result.data.id')
            ->assertDataKeysEqual($modelData, ['tags'])
            ->assertKeyChildrenCountEquals('result.data.tagged', 3)
            ->assertDataKeysEqual([
                'tagged.0.tag_slug' => $modelData['tags'][0],
                'tagged.1.tag_slug' => $modelData['tags'][1],
                'tagged.2.tag_slug' => $modelData['tags'][2],
            ])
            ->getId();

        // update by guest
        $this
            ->logout()
            ->request('PUT', "api/posts/{$postId}", [
                'json' => $modelData,
            ])
            ->assertStatus(403);

        // update by another user
        $this
            ->login(2)
            ->request('PUT', "api/posts/{$postId}", [
                'json' => $modelData,
            ])
            ->assertStatus(403);

        // delete by another user
        $this
            ->login(2)
            ->request('DELETE', "api/posts/{$postId}")
            ->assertStatus(403);

        // update with validation errors
        $this
            ->login(1)
            ->request('PUT', "api/posts/{$postId}", [
                'json' => [],
            ])
            ->assertStatus(400)
            ->assertKeyNotExists('result.data.id')
            ->assertKeysExist([
                'result.errors.title',
                'result.errors.intro',
                'result.errors.text',
            ]);

        // update by owner
        $this
            ->login(1)
            ->request('PUT', "api/posts/{$postId}", [
                'json' => $modifiedModelData,
            ])
            ->assertStatus(200)
            ->assertKeyEquals('result.data.id', $postId)
            ->assertDataKeysEqual($modifiedModelData, ['tags'])
            ->assertKeyChildrenCountEquals('result.data.tagged', 3)
            ->assertDataKeysEqual([
                'tagged.0.tag_slug' => $modifiedModelData['tags'][0],
                'tagged.1.tag_slug' => $modifiedModelData['tags'][1],
                'tagged.2.tag_slug' => $modifiedModelData['tags'][2],
            ]);

        // delete by owner
        $this
            ->login(1)
            ->request('DELETE', "api/posts/{$postId}")
            ->assertStatus(200);

        // request of deleted
        $this
            ->logout()
            ->request('GET', "api/posts/{$postId}")
            ->assertStatus(404)
            ->assertKeyNotExists('result.data.id');
    }

    /**
     * @return array
     */
    protected function modelData()
    {
        $tagNormalizer = config('tagging.normalizer');

        return [
            'title' => $this->faker->sentence,
            'intro' => $this->faker->paragraph,
            'text' => $this->faker->text,
            'tags' => array_map(function($item) use ($tagNormalizer){
                return call_user_func($tagNormalizer, $item);
            }, $this->faker->words(3)),
        ];
    }
}
