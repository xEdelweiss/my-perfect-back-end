<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $registerCommonFields = function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->unique();
            $table->text('intro')->nullable();
            $table->text('text');
            $table->integer('author_id');
            $table->timestamps();
        };

        Schema::create('posts', function (Blueprint $table) use ($registerCommonFields) {
            $registerCommonFields($table);
        });

        Schema::create('post_revisions', function (Blueprint $table) use ($registerCommonFields) {
            $registerCommonFields($table);
            $table->integer('base_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('posts');
        Schema::drop('post_revisions');
    }
}
