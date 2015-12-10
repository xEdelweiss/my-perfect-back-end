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
}
