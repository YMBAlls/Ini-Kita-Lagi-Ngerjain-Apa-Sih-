<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {

    public function up()
    {
        Schema::create('anggota', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 150);
            $table->string('email', 150)->nullable()->unique();
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_daftar')->default(DB::raw('(CURRENT_DATE)'));

        });
    }

    public function down()
    {
        Schema::dropIfExists('anggota');
    }
};
