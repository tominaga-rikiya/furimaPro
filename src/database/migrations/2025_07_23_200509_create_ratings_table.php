<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sold_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('score')->unsigned();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->unique(['sold_item_id', 'from_user_id']);
        });

        DB::statement('ALTER TABLE ratings ADD CONSTRAINT ratings_score_check CHECK (score >= 1 AND score <= 5)');
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
