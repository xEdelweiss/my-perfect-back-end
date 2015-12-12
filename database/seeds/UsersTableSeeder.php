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
        $this->truncateTable('users');
        $this->truncateTable('posts');

        // dev user
        \App\Models\User::create([
            'name' => 'me',
            'email' => 'me@me.me',
            'password' => bcrypt('mememe'),
            'remember_token' => str_random(10),
        ]);

        factory(App\Models\User::class, 49)->create()->each(function(User $user) {
            if (rand(0, 10) > 6) {
                $posts = factory(App\Models\Post::class, rand(0, 10))->make();
                foreach ($posts as $post) {
                    $user->posts()->save($post);
                }
            }
            // $output->writeln(" - user created: {$u->name}");
        });
    }
}
