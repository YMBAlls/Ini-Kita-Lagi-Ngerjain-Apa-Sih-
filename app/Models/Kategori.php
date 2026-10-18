<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    // @dbml-sync:table-property
    protected $table = 'kategori';
    // @enddbml-sync:table-property

    // @dbml-sync:fillable
    protected $fillable = [
        'nama',
		'deskripsi'
    ];
    // @enddbml-sync:fillable

    // @dbml-sync:casts
    protected $casts = [
        
    ];
    // @enddbml-sync:casts

    // @dbml-sync:relations
    public function buku()
    {
        return $this->hasMany(Buku::class, 'kategori_id', 'id');
    }

    // @enddbml-sync:relations
}