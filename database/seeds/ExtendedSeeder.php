<?php

use Illuminate\Database\Seeder;

class ExtendedSeeder extends Seeder
{
    /**
     * @param string $tableName
     */
    protected function truncateTable($tableName)
    {
        DB::table($tableName)->truncate();
    }

    /**
     * @return \Faker\Generator
     */
    protected function getFaker()
    {
        $locales = ['en_US', 'uk_UA', 'ru_RU'];
        $randomKey = array_rand($locales, 1);

        return Faker\Factory::create($locales[$randomKey]);
    }
}
