<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('anggota_id');
            $table->foreign('anggota_id')->references('id')->on('anggota');
            $table->unsignedInteger('buku_id');
            $table->foreign('buku_id')->references('id')->on('buku');
            $table->date('tanggal_pinjam')->default(DB::raw('(CURRENT_DATE)'));
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_kembali')->nullable();
            $table->string('status', 20)->default('dipinjam');

        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman');
    }
};
