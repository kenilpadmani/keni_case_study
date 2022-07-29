<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $module = new Product();
        Schema::create($module->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedInteger('category_id')->comment('primary key of category');
            $category = new Category();
            $table->foreign('category_id')->references('id')->on($category->getTable())->onDelete('cascade')->onUpdate('cascade');
            $table->string('description')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $module = new Product();
        Schema::dropIfExists($module->getTable());
    }
};
