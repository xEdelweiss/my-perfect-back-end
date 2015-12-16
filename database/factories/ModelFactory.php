<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var Illuminate\Database\Eloquent\Factory $factory */

$getFaker = function(){
    $locales = ['en_US', 'uk_UA', 'ru_RU'];
    $randomKey = array_rand($locales, 1);
    return Faker\Factory::create($locales[$randomKey]);
};

$factory->define(\App\Models\User::class, function() use ($getFaker) {
    /** @var Faker\Generator $faker */
    $faker = $getFaker();

    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(\App\Models\Post::class, function() use ($getFaker) {
    /** @var Faker\Generator $faker */
    $faker = $getFaker();

    return [
        'title' => $faker->sentence,
        'intro' => $faker->paragraph,
        'text' => $faker->text,
        'author_id' => factory(\App\Models\User::class)->create()->id,
    ];
});
