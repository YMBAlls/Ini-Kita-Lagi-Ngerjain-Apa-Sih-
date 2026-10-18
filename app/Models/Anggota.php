<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'anggota';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nama',
		'email',
		'no_telepon',
		'alamat',
		'tanggal_daftar'
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        'tanggal_daftar' => 'date'
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'anggota_id', 'id');
    }

    // @enddbml-sync:relations
}