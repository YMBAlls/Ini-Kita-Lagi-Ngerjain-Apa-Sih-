<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembeli', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama', 150);
            $table->string('email', 150)->nullable()->unique();
            $table->string('no_telepon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });

        Schema::create('produk_toko', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('buku_id')->unique();
            $table->foreign('buku_id')->references('id')->on('buku')->restrictOnDelete();
            $table->decimal('harga', 12, 2);
            $table->unsignedInteger('stok')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nomor_pesanan', 30)->unique();
            $table->unsignedInteger('pembeli_id');
            $table->foreign('pembeli_id')->references('id')->on('pembeli')->restrictOnDelete();
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->text('alamat_pengiriman');
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->timestamps();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pesanan_id');
            $table->foreign('pesanan_id')->references('id')->on('pesanan')->restrictOnDelete();
            $table->unsignedInteger('produk_toko_id');
            $table->foreign('produk_toko_id')->references('id')->on('produk_toko')->restrictOnDelete();
            $table->unsignedInteger('jumlah');
            $table->decimal('harga_satuan', 12, 2);
            $table->timestamps();

            $table->unique(['pesanan_id', 'produk_toko_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('produk_toko');
        Schema::dropIfExists('pembeli');
    }
};
