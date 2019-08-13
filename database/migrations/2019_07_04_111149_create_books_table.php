<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('book_title')->unique();
            $table->string('author')->nullable();
            $table->char('publishedDate', 10)->nullable();
            $table->char('isbn_13', 13)->nullable()->unique();
            $table->text('book_description')->nullable();
            $table->string('book_image_url')->nullable();
            $table->bigInteger('category_id')->unsigned()->index()->nullable();;
            $table->integer('content_count')->nullable();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
