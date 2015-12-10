<?php

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

        // dev user
        \App\Models\User::create([
            'name' => 'me',
            'email' => 'me@me.me',
            'password' => bcrypt('mememe'),
            'remember_token' => str_random(10),
        ]);

        factory(App\Models\User::class, 49)->create()->each(function($u) {
            // $output->writeln(" - user created: {$u->name}");
            // $u->posts()->save(factory(App\Models\User::class)->make());
        });
    }
}
