<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kategori_id');
            $table->foreign('kategori_id')->references('id')->on('kategori');
            $table->string('judul', 255);
            $table->string('penulis', 150);
            $table->string('penerbit', 150)->nullable();
            $table->smallInteger('tahun_terbit')->nullable();
            $table->string('isbn', 20)->nullable()->unique();
            $table->integer('stok')->default(1);

        });
    }

    public function down()
    {
        Schema::dropIfExists('buku');
    }
};
