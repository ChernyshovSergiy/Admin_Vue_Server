<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSizesTable extends Migration
{
    public function up(): void
    {
        Schema::create('sizes', static function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_active')->default(0);
            $table->string('value');
            $table->integer('sort')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
}
