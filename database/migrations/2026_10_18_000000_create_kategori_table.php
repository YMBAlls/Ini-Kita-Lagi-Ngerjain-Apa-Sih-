<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 100)->unique();
            $table->text('deskripsi')->nullable();

        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori');
    }
};
