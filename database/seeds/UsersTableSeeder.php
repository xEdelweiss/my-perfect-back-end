<?php

use App\Models\User;

class UsersTableSeeder extends ExtendedSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->clearTables();

        // dev user
        \App\Models\User::create([
            'name' => 'me',
            'email' => 'me@me.me',
            'password' => bcrypt('mememe'),
            'remember_token' => str_random(10),
        ]);

        $faker = $this->getFaker();
        $tagsSet = $faker->words(100);

        factory(App\Models\User::class, 49)->create()->each(function(User $user) use ($faker, $tagsSet) {
            if (rand(0, 10) > 6) {
                /** @var \App\Models\Post[] $posts */
                $posts = factory(App\Models\Post::class, rand(2, 10))->make();
                foreach ($posts as $post) {
                    $user->posts()->save($post);
                    $post->tag($faker->randomElements($tagsSet, rand(2, 7)));
                }
            }
            // $output->writeln(" - user created: {$u->name}");
        });
    }

    protected function clearTables()
    {
        $this->truncateTable('users');
        $this->truncateTable('posts');
        $this->truncateTable('tagging_tagged');
        $this->truncateTable('tagging_tags');
    }
}
