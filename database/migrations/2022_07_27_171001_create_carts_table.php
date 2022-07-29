<?php

use App\Models\Cart;
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
        $module = new Cart();
        Schema::create($module->getTable(), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('session_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $user = new User();
            $table->foreign('user_id')->references('id')->on($user->getTable())->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('product_id')->nullable();
            $product = new Product();
            $table->foreign('product_id')->references('id')->on($product->getTable())->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('qty')->nullable();
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
        $module = new Cart();
        Schema::dropIfExists($module->getTable());
    }
};
