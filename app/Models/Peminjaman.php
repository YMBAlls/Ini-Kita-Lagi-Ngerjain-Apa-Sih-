<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'peminjaman';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'anggota_id',
		'buku_id',
		'tanggal_pinjam',
		'tanggal_jatuh_tempo',
		'tanggal_kembali',
		'status'
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'tanggal_pinjam' => 'date',
		'tanggal_jatuh_tempo' => 'date',
		'tanggal_kembali' => 'date'
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id', 'id');
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'id');
    }

    // @enddbml-sync:relations
}