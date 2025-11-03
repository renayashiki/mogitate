<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSeasonTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_season', function (Blueprint $table) {
                // product_id (外部キー)
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                // season_id (外部キー)
                $table->foreignId('season_id')->constrained()->onDelete('cascade');

                // 複合主キーを設定
                $table->primary(['product_id', 'season_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_season');
    }
}
