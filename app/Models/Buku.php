<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'buku';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'kategori_id',
		'judul',
		'penulis',
		'penerbit',
		'tahun_terbit',
		'isbn',
		'stok'
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id', 'id');
    }

    // @enddbml-sync:relations
}