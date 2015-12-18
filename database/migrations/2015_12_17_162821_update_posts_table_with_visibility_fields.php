<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePostsTableWithVisibilityFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $registerCommonFields = function (Blueprint $table) {
            $table->boolean('is_draft')->default(false);
            $table->boolean('is_private')->default(false);
        };

        Schema::table('posts', $registerCommonFields);
        Schema::table('post_revisions', $registerCommonFields);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $dropCommonFields = function (Blueprint $table) {
            $table->removeColumn('is_draft');
            $table->removeColumn('is_private');
        };

        Schema::table('posts', $dropCommonFields);
        Schema::table('post_revisions', $dropCommonFields);
    }
}
